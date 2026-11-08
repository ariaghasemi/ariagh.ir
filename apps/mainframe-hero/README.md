# Mainframe® — hero landing

A full-screen hero landing page for the creative agency **Mainframe**, built with
React 19, TypeScript, Vite and Tailwind CSS v4. No other UI libraries.

```bash
npm install
npm run dev        # http://localhost:5173
npm run build      # tsc -b && vite build
npm run preview    # serve the production build
```

## Structure

```
index.html                 the two webfont stylesheets are linked here
src/
  main.tsx                 React root
  index.css                Tailwind entry, --font-heading / --font-body, caret keyframes
  App.tsx                  BackgroundVideo + Navbar + Hero
  hooks/useTypewriter.ts   { displayed, done }
  components/
    BackgroundVideo.tsx    mouse-scrubbed <video>
    Navbar.tsx             fixed bar, desktop links, hamburger + overlay
    Hero.tsx               blurred label, typewriter, action pills
```

## How the video scrub works

The clip is never played — it is **scrubbed** by horizontal pointer movement.

- `mousemove` tracks `prevX`, computes `delta = currentX - prevX`, and converts it
  to a time offset: `(delta / window.innerWidth) * SENSITIVITY * duration`
  with `SENSITIVITY = 0.8`.
- `targetTime` is clamped to `[0, duration]` and applied to `video.currentTime`.
- Only **one seek is in flight** at a time. `onSeeked` clears the flag and
  immediately picks up wherever the pointer has moved to since, so fast movement
  never floods the element with seek requests.
- `video.duration` is `NaN` until metadata arrives (and `Infinity` for live
  streams), so every read goes through a finite-duration guard.

Two practical notes:

- Because the video never plays, a clip sitting at exactly `0` can stay blank.
  `loadedmetadata` nudges `currentTime` off zero so the browser decodes and
  paints a frame.
- Autoplay is intentionally off, per spec. If you want the clip to run on its
  own until the pointer takes over, add `autoPlay` and drop the nudge.

## Timing

| Element | Timing |
| --- | --- |
| Typewriter | starts after `startDelay` 600ms, 38ms per character |
| Action pills | fade + slide up 400ms after load, independent of the typewriter |
| Caret blinks | `blink 1s step-end infinite`, hidden once `done` |

## Notes

- Fonts: `HelveticaNowDisplay-Medium` (heading, logo only) and
  `HelveticaNowDisplayW01-Rg` (body, everything else). Both are loaded from
  `db.onlinewebfonts.com` and exposed as `--font-heading` / `--font-body`.
- The copy button writes `hello@mainframe.co` to the clipboard via
  `navigator.clipboard.writeText()`.
- `prefers-reduced-motion` stops the caret from blinking.
