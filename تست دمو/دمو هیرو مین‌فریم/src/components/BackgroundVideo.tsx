import { useEffect, useRef } from 'react'

const VIDEO_SRC =
  'https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260826_041744_63efcd78-bf7d-4039-99e2-2461e8a61903.mp4'

const SENSITIVITY = 0.8

/**
 * ویدیوی تمام‌صفحه که با حرکت افقی ماوس جلو/عقب می‌شود (mouse-scrub).
 * جلوگیری از سیل seek: فقط یک seek همزمان؛ با onSeeked اگر target جا افتاده باشد seek بعدی صف می‌شود.
 */
export default function BackgroundVideo() {
  const videoRef = useRef<HTMLVideoElement>(null)
  const prevXRef = useRef<number | null>(null)
  const targetTimeRef = useRef(0)
  const seekingRef = useRef(false)

  useEffect(() => {
    const handleMouseMove = (e: MouseEvent) => {
      const video = videoRef.current
      const prevX = prevXRef.current
      prevXRef.current = e.clientX

      if (!video || !video.duration || prevX === null) return

      const delta = e.clientX - prevX
      const offset = (delta / window.innerWidth) * SENSITIVITY * video.duration
      const next = Math.min(
        Math.max(targetTimeRef.current + offset, 0),
        video.duration,
      )
      targetTimeRef.current = next

      if (!seekingRef.current) {
        seekingRef.current = true
        video.currentTime = next
      }
    }

    window.addEventListener('mousemove', handleMouseMove)
    return () => window.removeEventListener('mousemove', handleMouseMove)
  }, [])

  const handleSeeked = () => {
    const video = videoRef.current
    if (!video) return
    seekingRef.current = false
    // اگر در حین seek هدف جلو رفته، seek بعدی را همین‌جا انجام بده
    if (Math.abs(video.currentTime - targetTimeRef.current) > 0.05) {
      seekingRef.current = true
      video.currentTime = targetTimeRef.current
    }
  }

  return (
    <video
      ref={videoRef}
      className="fixed inset-0 z-0 h-full w-full object-cover object-[70%_center]"
      src={VIDEO_SRC}
      muted
      playsInline
      preload="auto"
      onSeeked={handleSeeked}
    />
  )
}
