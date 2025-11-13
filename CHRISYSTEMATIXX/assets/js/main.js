const initSite = () => {
  const prefersReduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'))
      if (target) {
        e.preventDefault()
        target.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    })
  })
  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('.bar > span').forEach(b => {
          const pct = b.getAttribute('data-pct') || '0'
          b.style.width = pct + '%'
        })
        io.unobserve(entry.target)
      }
    })
  }, { threshold: 0.3 })
  const skills = document.getElementById('skills')
  if (skills) io.observe(skills)

  const lb = document.createElement('div')
  lb.className = 'lightbox hidden'
  lb.setAttribute('role', 'dialog')
  lb.setAttribute('aria-modal', 'true')
  lb.setAttribute('aria-hidden', 'true')
  lb.innerHTML = '<div class="lightbox-backdrop"></div><div class="lightbox-content" tabindex="-1"><img alt=""><button class="lightbox-close" aria-label="Close">×</button></div>'
  document.body.appendChild(lb)
  const lbImg = lb.querySelector('img')
  const lbContent = lb.querySelector('.lightbox-content')
  const lbCloseBtn = lb.querySelector('.lightbox-close')
  let lastFocus = null
  let trapHandler = null
  const closeLb = () => {
    lb.classList.add('hidden')
    lb.setAttribute('aria-hidden', 'true')
    lbImg.src = ''
    if (trapHandler) { document.removeEventListener('keydown', trapHandler) }
    if (lastFocus && typeof lastFocus.focus === 'function') { lastFocus.focus() }
  }
  lb.querySelector('.lightbox-backdrop').addEventListener('click', closeLb)
  lbCloseBtn.addEventListener('click', closeLb)
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !lb.classList.contains('hidden')) closeLb() })
  document.addEventListener('click', e => {
    const img = e.target.closest('.certs .item img')
    if (img) {
      lbImg.src = img.getAttribute('src')
      lbImg.alt = img.getAttribute('alt') || ''
      lb.classList.remove('hidden')
      lb.setAttribute('aria-hidden', 'false')
      lastFocus = document.activeElement
      lbContent.focus()
      // Focus trap within lightbox
      const focusables = [lbCloseBtn]
      trapHandler = (ev) => {
        if (ev.key !== 'Tab') return
        ev.preventDefault()
        // Only one focusable (close button) for now
        focusables[0].focus()
      }
      document.addEventListener('keydown', trapHandler)
    }
  })

  const typed = document.getElementById('typed-name')
  if (typed) {
    const full = typed.getAttribute('data-text') || ''
    const reduce = prefersReduce
    if (reduce) {
      typed.textContent = full
      typed.classList.add('typing-done')
    } else {
      typed.textContent = ''
      let i = 0
      const speed = 80
      const tick = () => {
        if (i <= full.length) {
          typed.textContent = full.slice(0, i)
          i++
          setTimeout(tick, speed)
        } else {
          typed.classList.add('typing-done')
        }
      }
      setTimeout(tick, 300)
    }
  }
  
  // Theme toggle (dark/light) respecting prefers-color-scheme with persistence
  const root = document.documentElement
  const themeToggleBtn = document.getElementById('theme-toggle')
  const mm = window.matchMedia('(prefers-color-scheme: light)')
  const applyTheme = (mode) => {
    root.classList.remove('theme-light', 'theme-dark')
    if (mode === 'light') root.classList.add('theme-light')
    else if (mode === 'dark') root.classList.add('theme-dark')
  }
  const getInitialTheme = () => {
    const saved = localStorage.getItem('theme')
    if (saved === 'light' || saved === 'dark') return saved
    return mm.matches ? 'light' : 'dark'
  }
  let currentTheme = getInitialTheme()
  applyTheme(currentTheme)
  if (themeToggleBtn) {
    const sunSvg = '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
    const moonSvg = '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="2" fill="none"/></svg>'
    const setToggleUi = () => {
      const next = currentTheme === 'light' ? 'dark' : 'light'
      themeToggleBtn.innerHTML = currentTheme === 'light' ? sunSvg : moonSvg
      themeToggleBtn.setAttribute('aria-label', next === 'dark' ? 'Switch to dark theme' : 'Switch to light theme')
      themeToggleBtn.setAttribute('title', next === 'dark' ? 'Switch to dark theme' : 'Switch to light theme')
      themeToggleBtn.setAttribute('aria-pressed', currentTheme === 'light' ? 'true' : 'false')
    }
    setToggleUi()
    themeToggleBtn.addEventListener('click', () => {
      currentTheme = currentTheme === 'light' ? 'dark' : 'light'
      localStorage.setItem('theme', currentTheme)
      applyTheme(currentTheme)
      setToggleUi()
    })
  }
  mm.addEventListener('change', () => {
    const saved = localStorage.getItem('theme')
    if (saved !== 'light' && saved !== 'dark') {
      currentTheme = getInitialTheme()
      applyTheme(currentTheme)
    }
  })

  // Live programming-themed background (code rain)
  const canvas = document.getElementById('code-canvas')
  if (canvas) {
    const reduce = prefersReduce
    if (!reduce) {
      const ctx = canvas.getContext('2d')
      const dpr = Math.max(1, Math.min(2, window.devicePixelRatio || 1))
      let w = 0, h = 0
      let fontSize = 16
      let cols = 0
      let drops = []

      const keywords = [
        // PHP
        'php','echo','array','function','class','public','private','protected','use','namespace','return','new','if','else','foreach','try','catch','finally','static','extends','implements','trait',
        // C++
        'cpp','std','cout','cin','vector','string','int','double','float','bool','namespace','include','#define','template','auto','constexpr','virtual','override','public','private','protected','return','new','delete','if','else','for','while','switch','case',
        // JS/TS
        'js','let','const','var','function','class','export','import','return','await','async','Promise','map','filter','reduce','if','else','for','while','switch','case','yield',
        // Python
        'py','def','class','self','return','import','from','as','lambda','yield','async','await','with','try','except','finally','for','while','if','else','elif',
        // Java
        'java','public','private','protected','class','interface','extends','implements','return','new','static','void','int','String','if','else','for','while','switch','case'
      ]

      function setSize() {
        w = Math.floor(window.innerWidth)
        h = Math.floor(window.innerHeight)
        fontSize = Math.max(14, Math.min(20, Math.round(w / 80)))
        canvas.width = Math.floor(w * dpr)
        canvas.height = Math.floor(h * dpr)
        canvas.style.width = w + 'px'
        canvas.style.height = h + 'px'
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0)
        ctx.font = `${fontSize}px Poppins, monospace`
        cols = Math.ceil(w / fontSize)
        drops = Array(cols).fill(0).map(() => Math.floor(Math.random() * h / fontSize))
      }

      function draw() {
        // translucent fade for trail
        ctx.fillStyle = 'rgba(11, 15, 25, 0.18)'
        ctx.fillRect(0, 0, w, h)
        for (let i = 0; i < cols; i++) {
          const x = i * fontSize
          const y = drops[i] * fontSize
          const k = keywords[(i + drops[i]) % keywords.length]
          // gradient-ish tint alternating brand colors
          ctx.fillStyle = (i % 3 === 0) ? 'rgba(34, 211, 238, 0.55)' : (i % 3 === 1 ? 'rgba(79, 70, 229, 0.55)' : 'rgba(52, 211, 153, 0.55)')
          ctx.fillText(k, x, y)
          // random reset to top
          if (y > h && Math.random() > 0.975) {
            drops[i] = 0
          } else {
            drops[i] += 1
          }
        }
        rafId = window.requestAnimationFrame(draw)
      }

      let rafId = 0
      setSize()
      ctx.fillStyle = 'rgba(11, 15, 25, 1)'
      ctx.fillRect(0, 0, w, h)
      rafId = window.requestAnimationFrame(draw)

      let resizeTO = 0
      window.addEventListener('resize', () => {
        window.cancelAnimationFrame(rafId)
        clearTimeout(resizeTO)
        resizeTO = window.setTimeout(() => {
          setSize()
          rafId = window.requestAnimationFrame(draw)
        }, 120)
      })
    }
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSite)
} else {
  initSite()
}
