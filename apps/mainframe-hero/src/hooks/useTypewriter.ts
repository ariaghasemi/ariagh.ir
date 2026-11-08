import { useEffect, useState } from 'react';

/**
 * Reveals `text` one character at a time.
 *
 * @param text       the string to type out
 * @param speed      milliseconds per character
 * @param startDelay milliseconds to wait before the first character
 */
export function useTypewriter(text: string, speed = 38, startDelay = 600) {
  const [displayed, setDisplayed] = useState('');
  const [done, setDone] = useState(false);

  useEffect(() => {
    setDisplayed('');
    setDone(false);

    let index = 0;
    let interval: number | undefined;

    const timeout = window.setTimeout(() => {
      interval = window.setInterval(() => {
        index += 1;
        setDisplayed(text.slice(0, index));

        if (index >= text.length) {
          window.clearInterval(interval);
          interval = undefined;
          setDone(true);
        }
      }, speed);
    }, startDelay);

    return () => {
      window.clearTimeout(timeout);
      if (interval !== undefined) window.clearInterval(interval);
    };
  }, [text, speed, startDelay]);

  return { displayed, done };
}
