/**
 * mfh-video-lab.js
 *
 * Reads a video once and reports everything the widgets need to configure
 * themselves: duration, real dimensions, the first frame that actually has an
 * image on it, a poster generated from that frame, and a sensitivity derived
 * from the duration.
 *
 * Everything happens in the visitor's browser. No frame is uploaded, nothing
 * is written to the server, and results are cached per URL for the session.
 */
(function (window, document) {
	'use strict';

	var DEFAULTS = (window.MFH && window.MFH.defaults) || {};
	var COMFORT_SECONDS = DEFAULTS.comfortSeconds || 9.6;
	var LUMINANCE_FLOOR = DEFAULTS.luminanceFloor || 18;
	var SAMPLE_COUNT = DEFAULTS.sampleCount || 24;

	/** Results are cached per URL — a page can hold several video widgets. */
	var cache = Object.create(null);
	var pending = Object.create(null);

	function clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}

	/**
	 * An off-screen probe element.
	 */
	function createProbe() {
		var video = document.createElement('video');
		video.muted = true;
		video.playsInline = true;
		video.preload = 'auto';
		// Required to read pixels back. If the host does not send CORS headers
		// the canvas comes back tainted and we fall back to metadata only.
		video.crossOrigin = 'anonymous';
		video.setAttribute('aria-hidden', 'true');
		video.style.cssText =
			'position:absolute;left:-99999px;top:0;width:2px;height:2px;opacity:0;pointer-events:none;';
		document.body.appendChild(video);
		return video;
	}

	function dispose(video) {
		if (!video) return;
		try {
			video.pause();
		} catch (e) {
			/* nothing to pause */
		}
		video.removeAttribute('src');
		try {
			video.load();
		} catch (e) {
			/* some browsers complain about an empty src — harmless */
		}
		if (video.parentNode) video.parentNode.removeChild(video);
	}

	/**
	 * Load just enough of the video to read its metadata.
	 */
	function readMetadata(url) {
		return new Promise(function (resolve, reject) {
			var video = createProbe();
			var settled = false;
			var timer = window.setTimeout(function () {
				if (settled) return;
				settled = true;
				dispose(video);
				reject(new Error('timeout'));
			}, 20000);

			function finish(error, value) {
				if (settled) return;
				settled = true;
				window.clearTimeout(timer);
				video.removeEventListener('loadedmetadata', onMeta);
				video.removeEventListener('error', onError);
				if (error) {
					dispose(video);
					reject(error);
				} else {
					resolve(video);
				}
			}

			function onMeta() {
				var duration = video.duration;
				if (!isFinite(duration) || duration <= 0) {
					finish(new Error('no-duration'));
					return;
				}
				finish(null, video);
			}

			function onError() {
				finish(new Error('load-error'));
			}

			video.addEventListener('loadedmetadata', onMeta);
			video.addEventListener('error', onError);
			video.src = url;
		});
	}

	/**
	 * Mean luminance of the current frame, or null when pixels are unreadable.
	 *
	 * Downscaling to a thumbnail keeps this cheap even for long clips.
	 */
	function luminance(video, canvas, context, state) {
		if (!state.pixelAccess) return null;

		try {
			context.drawImage(video, 0, 0, canvas.width, canvas.height);
			var data = context.getImageData(0, 0, canvas.width, canvas.height).data;
			var total = 0;
			var pixels = 0;

			for (var i = 0; i < data.length; i += 4) {
				total += 0.2126 * data[i] + 0.7152 * data[i + 1] + 0.0722 * data[i + 2];
				pixels += 1;
			}

			return pixels ? total / pixels : null;
		} catch (error) {
			state.pixelAccess = false;
			return null;
		}
	}

	/**
	 * A poster-sized JPEG data URL of the current frame.
	 */
	function capturePoster(video, state) {
		if (!state.pixelAccess) return '';

		try {
			var scale = Math.min(1, 720 / (video.videoWidth || 720));
			var canvas = document.createElement('canvas');
			canvas.width = Math.max(2, Math.round((video.videoWidth || 720) * scale));
			canvas.height = Math.max(2, Math.round((video.videoHeight || 1280) * scale));

			var context = canvas.getContext('2d');
			context.drawImage(video, 0, 0, canvas.width, canvas.height);
			return canvas.toDataURL('image/jpeg', 0.72);
		} catch (error) {
			state.pixelAccess = false;
			return '';
		}
	}

	/**
	 * Walk through the clip and find the first frame with visible content.
	 */
	function findFirstLiveFrame(video, options) {
		return new Promise(function (resolve) {
			var duration = video.duration;
			var count = clamp(options.samples || SAMPLE_COUNT, 2, 96);
			var floor = typeof options.floor === 'number' ? options.floor : LUMINANCE_FLOOR;

			var canvas = document.createElement('canvas');
			canvas.width = 32;
			canvas.height = 18;
			var context = canvas.getContext('2d', { willReadFrequently: true });
			var state = { pixelAccess: !!context };

			var index = 0;
			var current = 0;
			var found = null;
			var settled = false;

			function finish() {
				if (settled) return;
				settled = true;
				video.removeEventListener('seeked', onSeeked);
				video.removeEventListener('error', onError);

				resolve({
					time: found ? found.time : 0,
					luminance: found ? found.luminance : null,
					poster: found ? found.poster : '',
					pixelAccess: state.pixelAccess,
				});
			}

			function step() {
				if (index >= count || found) {
					finish();
					return;
				}

				// Sample the opening of the clip more densely than the tail:
				// intros and fades from black live there.
				var ratio = index / (count - 1 || 1);
				current = Math.pow(ratio, 1.6) * duration;

				index += 1;

				try {
					video.currentTime = current;
				} catch (error) {
					finish();
				}
			}

			function onSeeked() {
				var value = luminance(video, canvas, context, state);

				if (value !== null && value >= floor) {
					found = {
						time: video.currentTime,
						luminance: value,
						poster: options.makePoster === false ? '' : capturePoster(video, state),
					};
					finish();
					return;
				}

				step();
			}

			function onError() {
				finish();
			}

			video.addEventListener('seeked', onSeeked);
			video.addEventListener('error', onError);
			step();
		});
	}

	/**
	 * Sensitivity: how much of the clip one full-width pointer sweep should
	 * cover. A comfortable sweep is about ten seconds; anything shorter than
	 * that fits inside a single sweep.
	 */
	function sensitivityFor(duration) {
		if (!isFinite(duration) || duration <= 0) return 0.8;
		return clamp(Math.min(1, COMFORT_SECONDS / duration), 0.05, 3);
	}

	/**
	 * Analyse a video.
	 *
	 * @param {string} url     Video URL.
	 * @param {Object} options { samples, floor, makePoster }
	 * @return {Promise<Object>}
	 */
	function analyse(url, options) {
		options = options || {};

		if (!url) return Promise.reject(new Error('no-url'));
		if (cache[url]) return Promise.resolve(cache[url]);
		if (pending[url]) return pending[url];

		var job = readMetadata(url)
			.then(function (video) {
				var duration = video.duration;
				var width = video.videoWidth || 0;
				var height = video.videoHeight || 0;

				return findFirstLiveFrame(video, options).then(function (frame) {
					dispose(video);

					var result = {
						duration: duration,
						width: width,
						height: height,
						aspect: height ? width / height : 0,
						initialTime: frame.time,
						luminance: frame.luminance,
						sensitivity: sensitivityFor(duration),
						poster: frame.poster,
						pixelAccess: frame.pixelAccess,
					};

					cache[url] = result;
					delete pending[url];
					return result;
				});
			})
			.catch(function (error) {
				delete pending[url];
				throw error;
			});

		pending[url] = job;
		return job;
	}

	window.MFHVideoLab = {
		analyse: analyse,
		sensitivityFor: sensitivityFor,
		forget: function (url) {
			delete cache[url];
		},
	};
})(window, document);
