import { useEffect, useState } from 'react'
import { useTypewriter } from '../hooks/useTypewriter'

const TYPEWRITER_TEXT =
  'Glad you stopped in. Good taste tends to find us. Now, what are we building?'

const PILLS = [
  'Pitch us an idea',
  'Come work here',
  'Send a brief hello',
  'See how we operate',
]

const EMAIL = 'hello@mainframe.co'

export default function Hero() {
  const { displayed, done } = useTypewriter(TYPEWRITER_TEXT, 38, 600)
  const [pillsVisible, setPillsVisible] = useState(false)

  // دکمه‌ها ۴۰۰ms بعد از لود — مستقل از تایپ‌نویser
  useEffect(() => {
    const t = window.setTimeout(() => setPillsVisible(true), 400)
    return () => window.clearTimeout(t)
  }, [])

  const copyEmail = () => {
    navigator.clipboard?.writeText(EMAIL).catch(() => {})
  }

  return (
    <section className="relative z-[1] flex h-screen flex-col justify-end overflow-hidden px-5 pb-12 sm:px-8 md:justify-center md:px-10 md:pb-0">
      <div className="relative z-10 max-w-xl">
        {/* ۱ — لیبل تار‌شده */}
        <div
          className="pointer-events-none mb-5 select-none text-white sm:mb-6"
          style={{
            fontSize: 'clamp(18px, 4vw, 26px)',
            lineHeight: 1.3,
            fontWeight: 400,
            filter: 'blur(4px)',
          }}
        >
          Hey there, meet A.R.I.A,
          <br />
          Mainframe&apos;s Adaptive Response Interface Agent
        </div>

        {/* ۲ — متن تایپ‌نویser */}
        <p
          className="mb-5 min-h-[54px] text-white sm:mb-6"
          style={{
            fontSize: 'clamp(18px, 4vw, 26px)',
            lineHeight: 1.35,
            fontWeight: 400,
          }}
        >
          {displayed}
          {!done && (
            <span
              className="cursor-blink ml-[2px] inline-block h-[1.1em] w-[2px] bg-white align-middle"
              aria-hidden="true"
            />
          )}
        </p>

        {/* ۳ — دکمه‌های قرصی */}
        <div
          className="flex flex-wrap gap-y-1"
          style={{
            opacity: pillsVisible ? 1 : 0,
            transform: pillsVisible ? 'translateY(0)' : 'translateY(8px)',
            transition: 'opacity 0.4s ease, transform 0.4s ease',
          }}
        >
          {PILLS.map((label) => (
            <button
              key={label}
              type="button"
              className="mx-[0.2em] mb-[0.4em] inline-flex items-center justify-center whitespace-nowrap rounded-full border border-black/10 bg-white px-4 py-[0.3em] text-[13px] text-black transition-colors duration-200 hover:bg-black hover:text-white sm:px-5 sm:text-[15px]"
            >
              {label}
            </button>
          ))}

          {/* قرص خط‌دار + آیکون کپی */}
          <button
            type="button"
            onClick={copyEmail}
            aria-label={`Copy ${EMAIL}`}
            className="mx-[0.2em] mb-[0.4em] inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full border border-white bg-transparent px-4 py-[0.3em] text-[13px] text-white transition-colors duration-200 hover:bg-white hover:text-black sm:gap-3 sm:px-5 sm:text-[15px]"
          >
            <span>
              Reach us:{' '}
              <span className="underline underline-offset-1">{EMAIL}</span>
            </span>
            <svg
              width="12"
              height="12"
              viewBox="0 0 12 12"
              fill="none"
              stroke="currentColor"
              strokeWidth="1"
              aria-hidden="true"
            >
              <rect x="4.25" y="4.25" width="6.5" height="6.5" rx="1.2" />
              <rect x="1.25" y="1.25" width="6.5" height="6.5" rx="1.2" />
            </svg>
          </button>
        </div>
      </div>
    </section>
  )
}
