import { useState } from 'react';

const LINKS = ['Labs', 'Studio', 'Openings', 'Shop'];

export default function Navbar() {
  const [open, setOpen] = useState(false);

  return (
    <>
      <header className="fixed inset-x-0 top-0 z-10 flex items-center justify-between px-5 py-4 sm:px-8 sm:py-5">
        {/* logo — the one place on the page that uses the heading face */}
        <a href="#" className="flex items-center gap-3" style={{ fontFamily: 'var(--font-heading)' }}>
          <span className="text-[21px] tracking-tight text-white sm:text-[26px]">Mainframe®</span>
          <span
            className="select-none text-[25px] text-white sm:text-[30px]"
            style={{ letterSpacing: '-0.02em' }}
          >
            ✳︎
          </span>
        </a>

        {/* desktop links, comma separated */}
        <nav className="hidden text-[23px] text-white md:flex">
          {LINKS.map((link, index) => (
            <span key={link}>
              <a href="#" className="transition-opacity hover:opacity-60">
                {link}
              </a>
              {index < LINKS.length - 1 ? ', ' : null}
            </span>
          ))}
        </nav>

        <a
          href="#"
          className="hidden text-[23px] text-white underline underline-offset-2 transition-opacity hover:opacity-60 md:block"
        >
          Get in touch
        </a>

        {/* hamburger — collapses into an X */}
        <button
          type="button"
          aria-label="Menu"
          aria-expanded={open}
          onClick={() => setOpen((value) => !value)}
          className="flex flex-col gap-[5px] md:hidden"
        >
          <span
            className={`h-[2px] w-6 bg-white transition-all duration-300 ${
              open ? 'translate-y-[7px] rotate-45' : ''
            }`}
          />
          <span
            className={`h-[2px] w-6 bg-white transition-all duration-300 ${
              open ? 'opacity-0' : 'opacity-100'
            }`}
          />
          <span
            className={`h-[2px] w-6 bg-white transition-all duration-300 ${
              open ? '-translate-y-[7px] -rotate-45' : ''
            }`}
          />
        </button>
      </header>

      {/* mobile overlay — sits under the bar (z-9) so the X stays reachable */}
      <div
        className={`fixed inset-0 z-[9] flex flex-col justify-center gap-8 bg-black/90 px-8 backdrop-blur-md transition-opacity duration-300 md:hidden ${
          open ? 'opacity-100' : 'opacity-0'
        }`}
        style={{ pointerEvents: open ? 'auto' : 'none' }}
      >
        {LINKS.map((link) => (
          <a key={link} href="#" className="text-left text-[32px] font-medium text-white">
            {link}
          </a>
        ))}
        <a
          href="#"
          className="text-left text-[32px] font-medium text-white underline underline-offset-2"
        >
          Get in touch
        </a>
      </div>
    </>
  );
}
