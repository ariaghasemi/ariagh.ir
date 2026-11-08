import { useEffect, useRef } from 'react';

const VIDEO_SRC =
  'https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260826_041744_63efcd78-bf7d-4039-99e2-2461e8a61903.mp4';

/** How much of the clip the pointer sweeps across one full window width. */
const SENSITIVITY = 0.8;

/** Below this the seek is a no-op, so we stop chasing the pointer. */
const MIN_SEEK_STEP = 0.01;

const clamp = (value: number, min: number, max: number) => Math.min(max, Math.max(min, value));

/**
 * A background video that is scrubbed — never played — by horizontal pointer
 * movement. Moving right runs it forward, moving left rewinds it.
 *
 * Seeking is expensive and browsers throttle it, so only one seek is ever in
 * flight: `onSeeked` picks up wherever the pointer has asked for in the
 * meantime instead of flooding the element with requests.
 */
export default function BackgroundVideo() {
  const videoRef = useRef<HTMLVideoElement>(null);
  const prevXRef = useRef<number | null>(null);
  const targetTimeRef = useRef(0);
  const seekingRef = useRef(false);

  useEffect(() => {
    const video = videoRef.current;
    if (!video) return;

    /** Duration is NaN until metadata arrives, and Infinity for live streams. */
    const duration = () => {
      const value = video.duration;
      return Number.isFinite(value) && value > 0 ? value : 0;
    };

    const requestSeek = () => {
      const total = duration();
      if (!total || seekingRef.current) return;

      const next = clamp(targetTimeRef.current, 0, total);
      if (Math.abs(next - video.currentTime) < MIN_SEEK_STEP) return;

      seekingRef.current = true;
      video.currentTime = next;
    };

    const handleLoadedMetadata = () => {
      targetTimeRef.current = 0;
      // The clip never plays, and a video sitting at exactly 0 can stay blank.
      // Nudging off zero is what makes the browser decode and paint a frame.
      if (video.currentTime === 0) video.currentTime = MIN_SEEK_STEP;
    };

    const handleSeeked = () => {
      seekingRef.current = false;
      requestSeek();
    };

    const handleMouseMove = (event: MouseEvent) => {
      const total = duration();
      if (!total) return;

      const currentX = event.clientX;
      const prevX = prevXRef.current;
      prevXRef.current = currentX;

      // The first move only seeds the origin; there is no delta to apply yet.
      if (prevX === null) return;

      const delta = currentX - prevX;
      const offset = (delta / window.innerWidth) * SENSITIVITY * total;

      targetTimeRef.current = clamp(targetTimeRef.current + offset, 0, total);
      requestSeek();
    };

    video.addEventListener('loadedmetadata', handleLoadedMetadata);
    video.addEventListener('seeked', handleSeeked);
    window.addEventListener('mousemove', handleMouseMove);

    return () => {
      video.removeEventListener('loadedmetadata', handleLoadedMetadata);
      video.removeEventListener('seeked', handleSeeked);
      window.removeEventListener('mousemove', handleMouseMove);
    };
  }, []);

  return (
    <video
      ref={videoRef}
      className="fixed inset-0 z-0 h-full w-full object-cover"
      style={{ objectPosition: '70% center' }}
      src={VIDEO_SRC}
      muted
      playsInline
      preload="auto"
    />
  );
}
