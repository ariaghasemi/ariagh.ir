/**
 * Mainframe Hero — Elementor editor bridge.
 *
 * Watches the character widget's video control. The moment a clip is chosen,
 * it is analysed in the browser and the panel is filled in: start time,
 * sensitivity, poster and a readable report.
 *
 * Nothing is uploaded and nothing is written to the server. The generated
 * poster lives only in the current editing session, which is why it is applied
 * to the preview rather than saved into the media library.
 *
 * Everything here is wrapped defensively: if Elementor's editor API or the
 * analysis library is unavailable, the panel simply stays manual.
 *
 * @package MainframeHero
 */

(function (window, document) {
	'use strict';

	var WIDGET = 'mfh-character';
	var I18N = (window.MFH_EDITOR && window.MFH_EDITOR.i18n) || {};

	/**
	 * Translate, falling back to a plain English default.
	 *
	 * @param {string} key     Message key.
	 * @param {string} fallback Fallback text.
	 * @return {string}
	 */
	function t(key, fallback) {
		return I18N[key] || fallback;
	}

	/**
	 * Round to two decimals for display.
	 *
	 * @param {number} value Number.
	 * @return {string}
	 */
	function fixed(value) {
		return (Math.round(value * 100) / 100).toFixed(2);
	}

	/**
	 * The video URL currently chosen in the panel, whichever source mode is on.
	 *
	 * @param {Object} settings Backbone settings model.
	 * @return {string}
	 */
	function videoUrl(settings) {
		var source = settings.get('mf_source_type') || 'library';
		var media = settings.get('mf_video');
		var url = settings.get('mf_video_url');

		if (source === 'url') {
			return (url && url.url) ? url.url : '';
		}

		return (media && media.url) ? media.url : '';
	}

	/**
	 * Write the analysis report into the panel's status block.
	 *
	 * @param {Object} html  HTML string to show.
	 * @param {Element} root The editor document.
	 * @return {void}
	 */
	function report(html, root) {
		var host = root.querySelector('[data-mfh-status]');

		if (!host) {
			return;
		}

		host.innerHTML = html;
	}

	/**
	 * Build the report markup.
	 *
	 * @param {Object} result Analysis result.
	 * @return {string}
	 */
	function summary(result) {
		var rows = [
			'<p class="mfh-doc__note">' + t('done', 'Auto-configuration applied.') + '</p>',
			'<ul class="mfh-doc__list">',
			'<li>' + t('duration', 'Duration') + ': ' + fixed(result.duration) + ' ' + t('seconds', 'seconds') + '</li>',
			'<li>' + t('frame', 'Start frame') + ': ' + fixed(result.initialTime) + ' ' + t('seconds', 'seconds') + '</li>',
			'<li>' + t('sens', 'Sensitivity') + ': ' + fixed(result.sensitivity) + '</li>'
		];

		if (result.width && result.height) {
			rows.push('<li>' + result.width + ' × ' + result.height + '</li>');
		}

		if (!result.pixelAccess) {
			rows.push('<li>' + t('noPixels', 'Frames could not be read (no CORS headers); only the duration was used.') + '</li>');
		}

		rows.push('</ul>');

		return rows.join('');
	}

	/**
	 * Analyse the chosen clip and push the results into the panel.
	 *
	 * @param {Object} settings Backbone settings model.
	 * @param {Element} root    The editor document.
	 * @return {void}
	 */
	function run(settings, root) {
		var url = videoUrl(settings);

		if (!url) {
			return;
		}

		if (!window.MFHVideoLab || typeof window.MFHVideoLab.analyse !== 'function') {
			return;
		}

		var auto = settings.get('mf_auto');

		report(
			'<p class="mfh-doc__note">' + t('analysing', 'Analysing the video…') + '</p>',
			root
		);

		window.MFHVideoLab.analyse(url, {
			samples: settings.get('mf_sample_count'),
			floor: settings.get('mf_luminance_floor'),
			makePoster: settings.get('mf_generate_poster') !== false && settings.get('mf_generate_poster') !== 'no'
		}).then(function (result) {
			report(summary(result), root);

			// Auto mode is on unless the user explicitly turned it off.
			if (auto === 'no' || auto === false) {
				return;
			}

			var patch = {
				mf_initial_time: result.initialTime,
				mf_sensitivity: result.sensitivity
			};

			settings.set(patch, { silent: false });

			// The generated poster is a data URL for this session only. Applying
			// it to the live element keeps the preview honest without storing a
			// megabyte-sized string in the post content.
			if (result.poster) {
				var nodes = document.querySelectorAll('[data-mfh-character]');

				Array.prototype.forEach.call(nodes, function (node) {
					var video = node.querySelector('video');

					if (video && video.getAttribute('src') === url) {
						video.setAttribute('poster', result.poster);
					}
				});
			}
		}).catch(function () {
			report(
				'<p class="mfh-doc__note mfh-doc__note--error">' +
				t('failed', 'I could not read that video. Check the address.') +
				'</p>',
				root
			);
		});
	}

	/**
	 * Attach to Elementor's editor once it exists.
	 *
	 * @return {void}
	 */
	function boot() {
		if (!window.elementor || !window.elementor.hooks) {
			return;
		}

		window.elementor.hooks.addAction(
			'panel/open_editor/widget/' + WIDGET,
			function (panel, model) {
				var settings = model && model.get ? model.get('settings') : null;

				if (!settings || !settings.on) {
					return;
				}

				// Analyse when the panel opens for a widget that already has a
				// clip, and again whenever the clip changes.
				run(settings, document);

				['mf_video', 'mf_video_url', 'mf_source_type', 'mf_luminance_floor', 'mf_sample_count']
					.forEach(function (key) {
						settings.on('change:' + key, function () {
							run(settings, document);
						});
					});
			}
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
}(window, document));
