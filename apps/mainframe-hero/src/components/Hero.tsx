import { useEffect, useState } from 'react';
import { useTypewriter } from '../hooks/useTypewriter';

const EMAIL = 'hello@mainframe.co';
const TYPED_TEXT = 'Glad you stopped in. Good taste tends to find us. Now, what are we building?';
const PILLS = ['Pitch us an idea', 'Come work here', 'Send a brief hello', 'See how we operate'];

/** Shared pill geometry — the fill and outline variants only differ in colour. */
const PILL =
  'mx-[0.2em] mb-[0.4em] inline-flex items-center justify-center whitespace-nowrap rounded-full border px-4 py-[0.3em] text-[13px] transition-colors duration-200 sm:px-5 sm:text-[15px]';

const BODY_TEXT = {
  fontSize: 'clamp(18px, 4vw, 26px)',
  fontWeight: 400,
} as const;

export default function Hero() {
  const { displayed, done } = useTypewriter(TYPED_TEXT);
  const [showPills, setShowPills] = useState(false);

  // The pills keep their own clock: they arrive 400ms after load whether or
  // not the typing has finished.
  useEffect(() => {
    const timer = window.setTimeout(() => setShowPills(true), 400);
    return () => window.clearTimeout(timer);
  }, []);

  const copyEmail = async () => {
    try {
      await navigator.clipboard.writeText(EMAIL);
    } catch {
      // Clipboard can be blocked by permissions or an insecure origin. The
      // address is printed on the button, so there is nothing to recover.
    }
  };

  return (
    <section className="relative z-[1] flex h-screen flex-col justify-end overflow-hidden px-5 pb-12 sm:px-8 md:justify-center md:px-10 md:pb-0">
      <div className="relative z-10 max-w-xl">
        {/* intro label, deliberately out of focus */}
        <p
          className="pointer-events-none mb-5 select-none sm:mb-6"
          style={{ ...BODY_TEXT, lineHeight: 1.3, color: '#fff', filter: 'blur(4px)' }}
        >
          Hey there, meet A.R.I.A,
          <br />
          Mainframe's Adaptive Response Interface Agent
        </p>

        {/* typewriter */}
        <p className="mb-5 text-white sm:mb-6" style={{ ...BODY_TEXT, lineHeight: 1.35, minHeight: '54px' }}>
          {displayed}
          {!done && (
            <span className="cursor-blink ml-[2px] inline-block h-[1.1em] w-[2px] bg-white align-middle" />
          )}
        </p>

        {/* actions */}
        <div
          className="flex flex-wrap gap-y-1"
          style={{
            opacity: showPills ? 1 : 0,
            transform: showPills ? 'translateY(0)' : 'translateY(8px)',
            transition: 'opacity 0.4s ease, transform 0.4s ease',
          }}
        >
          {PILLS.map((label) => (
            <a
              key={label}
              href="#"
              className={`${PILL} border-black/10 bg-white text-black hover:bg-black hover:text-white`}
            >
              {label}
            </a>
          ))}

          <button
            type="button"
            onClick={copyEmail}
            className={`${PILL} gap-2 border-white bg-transparent text-white hover:bg-white hover:text-black sm:gap-3`}
          >
            Reach us: <span className="underline underline-offset-1">{EMAIL}</span>
            <svg
              width="12"
              height="12"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
              aria-hidden="true"
            >
              <rect x="9" y="9" width="12" height="12" rx="2" />
              <rect x="3" y="3" width="12" height="12" rx="2" />
            </svg>
          </button>
        </div>
      </div>
    </section>
  );
}
