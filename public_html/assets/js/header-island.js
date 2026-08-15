(() => {
  'use strict';

  /*
  |--------------------------------------------------------------------------
  | Varenz Supplies Ltd — Premium Header Island Controller
  |--------------------------------------------------------------------------
  |
  | Owns:
  | - Header reveal choreography
  | - Simultaneous typed labels
  | - Desktop mega navigation
  | - Mega keyboard navigation
  | - Mobile drawer information architecture
  | - Navigation destinations
  | - Search hand-off
  | - Responsive cleanup
  | - Premium interaction hooks
  |
  | app.js continues to own:
  | - Mobile drawer open / close lifecycle
  | - Drawer focus trapping
  | - Shared body locking
  | - Search overlay lifecycle
  | - Team modal
  | - Global Escape handling
  |
  */

  const APP =
    window.VARENZ_APP
    && typeof window.VARENZ_APP === 'object'
      ? window.VARENZ_APP
      : {};

  const REDUCED_MOTION =
    window.matchMedia?.(
      '(prefers-reduced-motion: reduce)'
    ).matches === true;

  const FINE_POINTER =
    window.matchMedia?.(
      '(hover: hover) and (pointer: fine)'
    ).matches === true;

  const MOBILE_BREAKPOINT = 900;

  const TYPE_DURATION = 520;
  const HEADER_REVEAL_DELAY = 260;
  const MEGA_CLOSE_DELAY = 230;

  const qs = (
    selector,
    root = document
  ) => root.querySelector(selector);

  const qsa = (
    selector,
    root = document
  ) => Array.from(
    root.querySelectorAll(selector)
  );

  const text = value =>
    String(value ?? '').trim();


  /*
  |--------------------------------------------------------------------------
  | Menu Data
  |--------------------------------------------------------------------------
  |
  | Keep the choices deliberately grouped.
  | The header should expose a small number of high-level decisions.
  |
  */

  const menuData = {
    about: {
      kicker:
        'Company',

      title:
        'About Varenz',

      description:
        'Understand the organisation, people and operating approach behind Varenz healthcare supply support.',

      items: [
        {
          icon:
            'building',

          title:
            'About VSL',

          description:
            'Company purpose, operating approach and healthcare supply focus.'
        },

        {
          icon:
            'users',

          title:
            'Partners & Relationships',

          description:
            'See the organisations and professional relationships that support our work.'
        },

        {
          icon:
            'heart',

          title:
            'Industries We Serve',

          description:
            'Hospitals, clinics, pharmacies, NGOs, diagnostics and research programmes.'
        },

        {
          icon:
            'file',

          title:
            'Case Studies',

          description:
            'Explore the types of procurement challenges Varenz helps customers solve.'
        },

        {
          icon:
            'users',

          title:
            'Company Resources',

          description:
            'Open company information, procurement guidance and support resources.'
        },

        {
          icon:
            'grid',

          title:
            'Operations',

          description:
            'See how requirements move from review to quotation, delivery and follow-up.'
        }
      ]
    },


    solutions: {
      kicker:
        'Supply Solutions',

      title:
        'Solutions for Healthcare Organisations',

      description:
        'Reduce the effort between an unclear requirement and a professionally structured supply response.',

      items: [
        {
          icon:
            'building',

          title:
            'Institutional Procurement',

          description:
            'Multi-product support for hospitals, clinics, NGOs and healthcare programmes.'
        },

        {
          icon:
            'search',

          title:
            'Specialised Sourcing',

          description:
            'Support for reference-sensitive, uncommon or difficult-to-identify products.'
        },

        {
          icon:
            'grid',

          title:
            'Recurring Supply',

          description:
            'Coordinate repeat requirements with clearer replenishment planning.'
        },

        {
          icon:
            'file',

          title:
            'Clear Documentation',

          description:
            'Structured quotations and available supporting product information.'
        },

        {
          icon:
            'map',

          title:
            'Delivery Coordination',

          description:
            'Clear communication around confirmed dispatch and receiving requirements.'
        },

        {
          icon:
            'shield',

          title:
            'Product Guidance',

          description:
            'Improve product identification, specifications and inquiry quality.'
        }
      ]
    },


    products: {
      kicker:
        'Product Portfolio',

      title:
        'Priority Healthcare Categories',

      description:
        'Start with the clinical category and let Varenz help clarify the exact product, model, reference or specification.',

      items: [
        {
          icon:
            'grid',

          title:
            'Diagnostic & Imaging',

          description:
            'CT contrast media and selected imaging-related supply requirements.'
        },

        {
          icon:
            'heart',

          title:
            'Cardiology & Intervention',

          description:
            'Pacemakers, coronary stents and specialist cardiology enquiries.'
        },

        {
          icon:
            'box',

          title:
            'Enteral Feeding',

          description:
            'ENFit syringes, nasogastric tubes and related enteral support.'
        },

        {
          icon:
            'box',

          title:
            'Infusion & Administration',

          description:
            'Giving sets and selected fluid-administration products.'
        },

        {
          icon:
            'shield',

          title:
            'Urology & Irrigation',

          description:
            'Selected sterile irrigation and procedural supply requirements.'
        },

        {
          icon:
            'file',

          title:
            'General Medical Supplies',

          description:
            'Routine institutional requirements and product-list review.'
        }
      ]
    },


    resources: {
      kicker:
        'Knowledge Centre',

      title:
        'Resources for Better Procurement Decisions',

      description:
        'Useful information should reduce uncertainty before a customer needs to contact the team.',

      items: [
        {
          icon:
            'file',

          title:
            'Product Guides',

          description:
            'Product and specification guidance for better prepared enquiries.'
        },

        {
          icon:
            'file',

          title:
            'Procurement Checklists',

          description:
            'Know what information improves quotation and requirement review.'
        },

        {
          icon:
            'shield',

          title:
            'Quality & Compliance',

          description:
            'Understand the Varenz documentation and quality-focused supply approach.'
        },

        {
          icon:
            'building',

          title:
            'Company Profile',

          description:
            'Access the formal Varenz company and capability overview.'
        },

        {
          icon:
            'grid',

          title:
            'FAQs',

          description:
            'Quick answers covering quotations, products, documents and support.'
        },

        {
          icon:
            'search',

          title:
            'Search Website',

          description:
            'Find products, procurement information, organisations and team members.'
        }
      ]
    },


    contact: {
      kicker:
        'Connect With Varenz',

      title:
        'Choose the Fastest Route',

      description:
        'Route enquiries according to what you need instead of forcing every customer through one generic contact path.',

      items: [
        {
          icon:
            'message',

          title:
            'Product Enquiries',

          description:
            'Availability, specifications, references and quotation requests.'
        },

        {
          icon:
            'building',

          title:
            'Institutional Procurement',

          description:
            'Multi-product and recurring organisational requirements.'
        },

        {
          icon:
            'shield',

          title:
            'Quality Concerns',

          description:
            'Product, service, documentation or delivery feedback.'
        },

        {
          icon:
            'users',

          title:
            'Partnerships',

          description:
            'Supplier, manufacturer, institutional and strategic collaboration.'
        },

        {
          icon:
            'briefcase',

          title:
            'Professional Opportunities',

          description:
            'Careers, professional introductions and future opportunities.'
        },

        {
          icon:
            'phone',

          title:
            'Call the Team',

          description:
            'Reach Varenz directly through the published telephone line.'
        }
      ]
    }
  };


  /*
  |--------------------------------------------------------------------------
  | SVG Sprite Mapping
  |--------------------------------------------------------------------------
  */

  const iconHref = {
    phone:
      '#i-phone',

    mail:
      '#i-mail',

    map:
      '#i-map',

    search:
      '#i-search',

    message:
      '#i-message',

    building:
      '#i-building',

    users:
      '#i-users',

    box:
      '#i-box',

    shield:
      '#i-shield',

    file:
      '#i-file',

    heart:
      '#i-heart',

    grid:
      '#i-grid',

    briefcase:
      '#i-briefcase',

    arrow:
      '#i-arrow-right'
  };


  /*
  |--------------------------------------------------------------------------
  | Published Contact Destinations
  |--------------------------------------------------------------------------
  |
  | Read the rendered PHP contact links instead of hard-coding telephone
  | numbers into JavaScript.
  |
  */

  function publishedPhoneHref() {
    const element =
      qs(
        '.utility-bubbles a[href^="tel:"], '
        + '.vsl-footer-contact a[href^="tel:"]'
      );

    return element
      ?.getAttribute(
        'href'
      )
      || '#cta';
  }


  function publishedEmailHref() {
    const element =
      qs(
        '.utility-bubbles a[href^="mailto:"], '
        + '.vsl-footer-contact a[href^="mailto:"]'
      );

    return element
      ?.getAttribute(
        'href'
      )
      || 'mailto:info@varenzsupplies.com';
  }


  /*
  |--------------------------------------------------------------------------
  | Navigation Destinations
  |--------------------------------------------------------------------------
  */

  function sectionLink(title) {
    const base = text(APP.baseUrl || '/').replace(/\/?$/, '/');
    const products = `${base}products`;
    const links = {
      Home:
        '#hero',

      'About VSL':
        `${base}about`,

      'Partners & Relationships':
        `${base}partners`,

      'Industries We Serve':
        `${base}about#organizations`,

      'Case Studies':
        `${base}about#approach`,

      'Company Resources':
        `${base}resources`,

      Operations:
        `${base}procurement`,

      'Institutional Procurement':
        `${base}procurement`,

      'Specialised Sourcing':
        products,

      'Recurring Supply':
        `${base}procurement`,

      'Clear Documentation':
        `${base}quality-compliance`,

      'Delivery Coordination':
        `${base}procurement`,

      'Product Guidance':
        products,

      'Diagnostic & Imaging':
        `${products}?category=Imaging`,

      'Cardiology & Intervention':
        `${products}?category=Cardiology`,

      'Enteral Feeding':
        `${products}?category=Enteral`,

      'Infusion & Administration':
        `${products}?category=Infusion`,

      'Urology & Irrigation':
        `${products}?category=Urology`,

      'General Medical Supplies':
        products,

      'Product Guides':
        `${base}resources`,

      'Procurement Checklists':
        `${base}procurement`,

      'Quality & Compliance':
        `${base}quality-compliance`,

      'Company Profile':
        `${text(APP.baseUrl || '/').replace(/\/?$/, '/')}`
        + 'assets/downloads/company-profile.pdf',

      FAQs:
        `${base}faq`,

      'Search Website':
        '#siteSearchInput',

      'Product Enquiries':
        `${base}contact`,

      'Quality Concerns':
        `${base}contact`,

      Partnerships:
        `${base}partners`,

      'Professional Opportunities':
        `${base}contact`,

      'Call the Team':
        publishedPhoneHref(),

      'Email Varenz':
        publishedEmailHref()
    };

    return links[title]
      || '#cta';
  }


  /*
  |--------------------------------------------------------------------------
  | Core Elements
  |--------------------------------------------------------------------------
  */

  const siteHeader =
    qs('#siteHeader');

  const headerIsland =
    qs('#headerIsland');

  const megaPanel =
    qs('#megaPanel');

  const megaScrim =
    qs('#megaScrim');

  const megaKicker =
    qs('#megaKicker');

  const megaTitle =
    qs('#megaTitle');

  const megaDescription =
    qs('#megaDescription');

  const megaGrid =
    qs('#megaGrid');

  const triggers =
    qsa('.mega-trigger');

  const drawer =
    qs('#mobileDrawer');

  const drawerNav =
    qs('#drawerNav');

  const navigationBubbles =
    qsa(
      '.nav-bubble'
    );

  let activeMenu = null;

  let closeTimer = 0;

  let typingFrame = 0;
  let typingStarted = false;

  let headerRevealTimer = 0;
  let headerFallbackTimer = 0;

  let resizeFrame = 0;
  let scrollFrame = 0;


  /*
  |--------------------------------------------------------------------------
  | Premium Header Entrance
  |--------------------------------------------------------------------------
  */

  function finishTyping() {
    qsa('.typed')
      .forEach(target => {
        target.textContent =
          target.dataset.type
          || '';
      });

    document.body.classList.remove(
      'cursor-on',
      'typing-mobile-support'
    );

    document.body.classList.add(
      'header-typing-complete'
    );
  }


  function startTypingAll() {
    if (typingStarted) {
      return;
    }

    typingStarted = true;

    const targets =
      qsa('.typed');

    if (!targets.length) {
      return;
    }

    if (REDUCED_MOTION) {
      finishTyping();
      return;
    }

    const startedAt =
      performance.now();

    document.body.classList.add(
      'cursor-on',
      'typing-mobile-support'
    );

    const frame = now => {
      const progress =
        Math.min(
          1,
          (
            now
            - startedAt
          )
          / TYPE_DURATION
        );

      /*
       * Smooth cubic easing for character reveal.
       */
      const eased =
        1
        - Math.pow(
          1 - progress,
          3
        );

      targets.forEach(target => {
        const fullText =
          target.dataset.type
          || '';

        const visibleCharacters =
          Math.floor(
            fullText.length
            * eased
          );

        target.textContent =
          fullText.slice(
            0,
            visibleCharacters
          );
      });

      if (progress < 1) {
        typingFrame =
          window.requestAnimationFrame(
            frame
          );

        return;
      }

      typingFrame = 0;

      finishTyping();
    };

    typingFrame =
      window.requestAnimationFrame(
        frame
      );
  }


  function revealHeader() {
    document.body.classList.add(
      'bubbles-ready'
    );

    siteHeader?.classList.add(
      'is-ready'
    );

    if (REDUCED_MOTION) {
      startTypingAll();
      return;
    }

    const firstBubble =
      qs('.bubble');

    if (!firstBubble) {
      startTypingAll();
      return;
    }

    headerFallbackTimer =
      window.setTimeout(
        startTypingAll,
        660
      );

    firstBubble.addEventListener(
      'animationend',
      () => {
        window.clearTimeout(
          headerFallbackTimer
        );

        headerFallbackTimer = 0;

        startTypingAll();
      },
      {
        once: true
      }
    );
  }


  if (REDUCED_MOTION) {
    document.body.classList.add(
      'bubbles-ready'
    );

    siteHeader?.classList.add(
      'is-ready'
    );

    finishTyping();
  } else {
    headerRevealTimer =
      window.setTimeout(
        revealHeader,
        HEADER_REVEAL_DELAY
      );
  }


  /*
  |--------------------------------------------------------------------------
  | Header Scroll State
  |--------------------------------------------------------------------------
  |
  | CSS can use:
  |
  | .site-header.is-scrolled
  | .site-header.is-condensed
  |
  */

  function updateHeaderScrollState() {
    scrollFrame = 0;

    const y =
      window.scrollY
      || document.documentElement
        .scrollTop
      || 0;

    siteHeader?.classList.toggle(
      'is-scrolled',
      y > 18
    );

    siteHeader?.classList.toggle(
      'is-condensed',
      y > 160
    );

    document.body.classList.toggle(
      'vsl-page-scrolled',
      y > 18
    );
  }


  window.addEventListener(
    'scroll',
    () => {
      if (scrollFrame) {
        return;
      }

      scrollFrame =
        window.requestAnimationFrame(
          updateHeaderScrollState
        );
    },
    {
      passive: true
    }
  );

  updateHeaderScrollState();


  /*
  |--------------------------------------------------------------------------
  | Premium Pointer Lighting
  |--------------------------------------------------------------------------
  |
  | CSS owns the glass/neumorphic visual treatment.
  |
  | JS only supplies:
  | --vhi-pointer-x
  | --vhi-pointer-y
  |
  */

  if (
    FINE_POINTER
    && !REDUCED_MOTION
    && headerIsland
  ) {
    headerIsland.addEventListener(
      'pointermove',
      event => {
        const rect =
          headerIsland
            .getBoundingClientRect();

        if (
          rect.width <= 0
          || rect.height <= 0
        ) {
          return;
        }

        const x =
          (
            event.clientX
            - rect.left
          )
          / rect.width;

        const y =
          (
            event.clientY
            - rect.top
          )
          / rect.height;

        headerIsland.style
          .setProperty(
            '--vhi-pointer-x',
            `${x * 100}%`
          );

        headerIsland.style
          .setProperty(
            '--vhi-pointer-y',
            `${y * 100}%`
          );
      }
    );

    headerIsland.addEventListener(
      'pointerleave',
      () => {
        headerIsland.style
          .removeProperty(
            '--vhi-pointer-x'
          );

        headerIsland.style
          .removeProperty(
            '--vhi-pointer-y'
          );
      }
    );
  }


  /*
  |--------------------------------------------------------------------------
  | Safe SVG Factory
  |--------------------------------------------------------------------------
  */

  function iconElement(
    iconName
  ) {
    const svg =
      document.createElementNS(
        'http://www.w3.org/2000/svg',
        'svg'
      );

    svg.setAttribute(
      'class',
      'icon'
    );

    svg.setAttribute(
      'aria-hidden',
      'true'
    );

    const use =
      document.createElementNS(
        'http://www.w3.org/2000/svg',
        'use'
      );

    use.setAttribute(
      'href',
      iconHref[iconName]
      || '#i-grid'
    );

    svg.appendChild(use);

    return svg;
  }


  /*
  |--------------------------------------------------------------------------
  | Mega Card Factory
  |--------------------------------------------------------------------------
  */

  function makeMegaCard(
    item,
    index
  ) {
    const link =
      document.createElement(
        'a'
      );

    link.className =
      'mega-card vsl-mega-card';

    link.href =
      sectionLink(
        item.title
      );

    link.dataset.megaLink =
      '';

    link.style.setProperty(
      '--mega-card-index',
      String(index)
    );

    if (
      item.title
      === 'Search Website'
    ) {
      link.dataset.searchLink =
        '';
    }

    /*
     * Company profile remains a normal link.
     * Browsers can display or download it according
     * to the response headers and user preference.
     *
     * Do not force download here because the PDF
     * may be intended for viewing on mobile.
     */

    const iconShell =
      document.createElement(
        'span'
      );

    iconShell.className =
      'mega-icon';

    iconShell.appendChild(
      iconElement(
        item.icon
      )
    );

    const copy =
      document.createElement(
        'span'
      );

    copy.className =
      'mega-card-copy';

    const heading =
      document.createElement(
        'h3'
      );

    heading.textContent =
      item.title;

    const paragraph =
      document.createElement(
        'p'
      );

    paragraph.textContent =
      item.description;

    const action =
      document.createElement(
        'span'
      );

    action.className =
      'mega-card-action';

    action.setAttribute(
      'aria-hidden',
      'true'
    );

    action.appendChild(
      iconElement(
        'arrow'
      )
    );

    copy.append(
      heading,
      paragraph
    );

    link.append(
      iconShell,
      copy,
      action
    );

    return link;
  }


  /*
  |--------------------------------------------------------------------------
  | Mega Menu Building
  |--------------------------------------------------------------------------
  */

  function buildMega(key) {
    const data =
      menuData[key];

    if (
      !data
      || !megaPanel
      || !megaGrid
    ) {
      return false;
    }

    if (megaKicker) {
      megaKicker.textContent =
        data.kicker;
    }

    if (megaTitle) {
      megaTitle.textContent =
        data.title;
    }

    if (megaDescription) {
      megaDescription.textContent =
        data.description;
    }

    megaGrid.replaceChildren(
      ...data.items.map(
        makeMegaCard
      )
    );

    megaPanel.dataset.menu =
      key;

    megaPanel.style.setProperty(
      '--mega-card-count',
      String(
        data.items.length
      )
    );

    return true;
  }


  function triggerForKey(key) {
    return triggers.find(
      trigger =>
        trigger.dataset.menu
        === key
    )
    || null;
  }


  function setTriggerStates(
    selectedTrigger = null
  ) {
    triggers.forEach(trigger => {
      const selected =
        trigger
        === selectedTrigger;

      trigger.setAttribute(
        'aria-expanded',
        selected
          ? 'true'
          : 'false'
      );

      trigger.classList.toggle(
        'is-open',
        selected
      );
    });
  }


  /*
  |--------------------------------------------------------------------------
  | Mega Open / Close
  |--------------------------------------------------------------------------
  */

  function openMega(trigger) {
    if (
      window.innerWidth
      <= MOBILE_BREAKPOINT
      || !trigger
      || !megaPanel
    ) {
      return;
    }

    window.clearTimeout(
      closeTimer
    );

    closeTimer = 0;

    const key =
      trigger.dataset.menu;

    if (
      !key
      || !menuData[key]
    ) {
      return;
    }

    if (!buildMega(key)) {
      return;
    }

    activeMenu = key;

    setTriggerStates(
      trigger
    );

    megaPanel.classList.add(
      'is-open'
    );

    megaScrim?.classList.add(
      'is-open'
    );

    megaPanel.setAttribute(
      'aria-hidden',
      'false'
    );

    document.body.classList.add(
      'mega-menu-open'
    );

    headerIsland?.classList.add(
      'has-open-mega'
    );

    headerIsland?.setAttribute(
      'data-active-menu',
      key
    );

    /*
     * Let the CSS begin from a known animated state.
     */
    window.requestAnimationFrame(
      () => {
        megaPanel.classList.add(
          'is-settled'
        );
      }
    );
  }


  function closeMega({
    force = false,
    restoreFocus = false
  } = {}) {
    window.clearTimeout(
      closeTimer
    );

    closeTimer = 0;

    if (
      !force
      && (
        megaPanel?.matches(
          ':hover'
        )
        || megaPanel?.contains(
          document.activeElement
        )
      )
    ) {
      return;
    }

    const previousMenu =
      activeMenu;

    activeMenu = null;

    setTriggerStates();

    megaPanel?.classList.remove(
      'is-open',
      'is-settled'
    );

    megaScrim?.classList.remove(
      'is-open'
    );

    megaPanel?.setAttribute(
      'aria-hidden',
      'true'
    );

    document.body.classList.remove(
      'mega-menu-open'
    );

    headerIsland?.classList.remove(
      'has-open-mega'
    );

    headerIsland?.removeAttribute(
      'data-active-menu'
    );

    if (
      restoreFocus
      && previousMenu
    ) {
      triggerForKey(
        previousMenu
      )?.focus();
    }
  }


  function scheduleMegaClose(
    delay = MEGA_CLOSE_DELAY
  ) {
    window.clearTimeout(
      closeTimer
    );

    closeTimer =
      window.setTimeout(
        () => {
          closeTimer = 0;

          closeMega();
        },
        delay
      );
  }


  /*
  |--------------------------------------------------------------------------
  | Mega Keyboard Navigation
  |--------------------------------------------------------------------------
  */

  function megaFocusableItems() {
    if (!megaPanel) {
      return [];
    }

    return qsa(
      'a[href]:not([hidden]), button:not([disabled]):not([hidden])',
      megaPanel
    ).filter(element =>
      element.getClientRects()
        .length > 0
    );
  }


  function focusFirstMegaItem() {
    megaFocusableItems()[0]
      ?.focus();
  }


  function moveMegaFocus(
    current,
    direction
  ) {
    const items =
      megaFocusableItems();

    if (!items.length) {
      return;
    }

    const currentIndex =
      Math.max(
        0,
        items.indexOf(
          current
        )
      );

    const nextIndex =
      (
        currentIndex
        + direction
        + items.length
      )
      % items.length;

    items[
      nextIndex
    ]?.focus();
  }


  /*
  |--------------------------------------------------------------------------
  | Desktop Trigger Events
  |--------------------------------------------------------------------------
  */

  triggers.forEach(
    (trigger, index) => {
      trigger.addEventListener(
        'pointerenter',
        () => {
          openMega(
            trigger
          );
        }
      );

      trigger.addEventListener(
        'focusin',
        () => {
          openMega(
            trigger
          );
        }
      );

      trigger.addEventListener(
        'click',
        event => {
          event.preventDefault();
          event.stopPropagation();

          const currentlyOpen =
            activeMenu
              === trigger.dataset.menu
            && megaPanel
              ?.classList
              .contains(
                'is-open'
              );

          if (currentlyOpen) {
            closeMega({
              force: true
            });

            return;
          }

          openMega(
            trigger
          );
        }
      );

      trigger.addEventListener(
        'pointerleave',
        () => {
          scheduleMegaClose(
            270
          );
        }
      );

      trigger.addEventListener(
        'keydown',
        event => {
          if (
            event.key
            === 'ArrowDown'
          ) {
            event.preventDefault();

            openMega(
              trigger
            );

            focusFirstMegaItem();

            return;
          }

          if (
            event.key
            === 'ArrowRight'
          ) {
            event.preventDefault();

            triggers[
              (
                index + 1
              )
              % triggers.length
            ]?.focus();

            return;
          }

          if (
            event.key
            === 'ArrowLeft'
          ) {
            event.preventDefault();

            triggers[
              (
                index
                - 1
                + triggers.length
              )
              % triggers.length
            ]?.focus();

            return;
          }

          if (
            event.key
            === 'Escape'
          ) {
            event.preventDefault();

            closeMega({
              force: true,
              restoreFocus: true
            });
          }
        }
      );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | Mega Panel Events
  |--------------------------------------------------------------------------
  */

  megaPanel?.addEventListener(
    'pointerenter',
    () => {
      window.clearTimeout(
        closeTimer
      );

      closeTimer = 0;
    }
  );


  megaPanel?.addEventListener(
    'pointerleave',
    () => {
      scheduleMegaClose();
    }
  );


  megaPanel?.addEventListener(
    'focusin',
    () => {
      window.clearTimeout(
        closeTimer
      );

      closeTimer = 0;
    }
  );


  megaPanel?.addEventListener(
    'focusout',
    event => {
      const related =
        event.relatedTarget;

      if (
        !megaPanel.contains(
          related
        )
        && !triggers.some(
          trigger =>
            trigger.contains(
              related
            )
        )
      ) {
        scheduleMegaClose(
          120
        );
      }
    }
  );


  megaPanel?.addEventListener(
    'keydown',
    event => {
      const target =
        event.target.closest(
          'a,button'
        );

      if (!target) {
        return;
      }

      if (
        event.key
        === 'ArrowRight'
      ) {
        event.preventDefault();

        moveMegaFocus(
          target,
          1
        );

        return;
      }

      if (
        event.key
        === 'ArrowLeft'
      ) {
        event.preventDefault();

        moveMegaFocus(
          target,
          -1
        );

        return;
      }

      if (
        event.key
        === 'ArrowDown'
      ) {
        event.preventDefault();

        moveMegaFocus(
          target,
          1
        );

        return;
      }

      if (
        event.key
        === 'ArrowUp'
      ) {
        event.preventDefault();

        moveMegaFocus(
          target,
          -1
        );

        return;
      }

      if (
        event.key
        === 'Home'
      ) {
        event.preventDefault();

        megaFocusableItems()[0]
          ?.focus();

        return;
      }

      if (
        event.key
        === 'End'
      ) {
        event.preventDefault();

        const items =
          megaFocusableItems();

        items[
          items.length - 1
        ]?.focus();

        return;
      }

      if (
        event.key
        === 'Escape'
      ) {
        event.preventDefault();

        closeMega({
          force: true,
          restoreFocus: true
        });
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | Mega Navigation Selection
  |--------------------------------------------------------------------------
  */

  function navigateHash(
    hash
  ) {
    if (
      !hash
      || !hash.startsWith('#')
    ) {
      return false;
    }

    const target =
      qs(hash);

    if (!target) {
      return false;
    }

    target.scrollIntoView({
      behavior:
        REDUCED_MOTION
          ? 'auto'
          : 'smooth',

      block:
        'start'
    });

    try {
      history.replaceState(
        null,
        '',
        hash
      );
    } catch (_) {
      /*
       * History manipulation is optional.
       */
    }

    return true;
  }


  megaPanel?.addEventListener(
    'click',
    event => {
      const link =
        event.target.closest(
          '[data-mega-link]'
        );

      if (!link) {
        return;
      }

      if (
        link.matches(
          '[data-search-link]'
        )
      ) {
        event.preventDefault();

        closeMega({
          force: true
        });

        qs('#openSearch')
          ?.click();

        return;
      }

      const href =
        link.getAttribute(
          'href'
        )
        || '';

      if (
        href.startsWith('#')
      ) {
        event.preventDefault();

        closeMega({
          force: true,
          restoreFocus: true
        });

        navigateHash(
          href
        );

        return;
      }

      closeMega({
        force: true
      });
    }
  );


  megaScrim?.addEventListener(
    'click',
    () => {
      closeMega({
        force: true
      });
    }
  );


  document.addEventListener(
    'click',
    event => {
      if (
        activeMenu
        && !headerIsland?.contains(
          event.target
        )
      ) {
        closeMega({
          force: true
        });
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | Mobile Drawer Factories
  |--------------------------------------------------------------------------
  |
  | This controller builds the mobile navigation structure only.
  |
  | app.js owns opening/closing the drawer itself.
  |
  */

  function drawerLink(
    title,
    className = ''
  ) {
    const link =
      document.createElement(
        'a'
      );

    if (className) {
      link.className =
        className;
    }

    link.href =
      sectionLink(
        title
      );

    link.textContent =
      title;

    if (
      title
      === 'Search Website'
    ) {
      link.dataset.searchLink =
        '';
    }

    return link;
  }


  function closeOtherDrawerGroups(
    current
  ) {
    if (!drawerNav) {
      return;
    }

    qsa(
      '.drawer-group',
      drawerNav
    ).forEach(group => {
      if (
        group === current
      ) {
        return;
      }

      group.classList.remove(
        'is-open'
      );

      qs(
        'button.drawer-trigger',
        group
      )?.setAttribute(
        'aria-expanded',
        'false'
      );
    });
  }


  function makeDrawerGroup(
    label,
    key = null,
    groupIndex = 0
  ) {
    const group =
      document.createElement(
        'div'
      );

    group.className =
      'drawer-group';

    group.style.setProperty(
      '--drawer-index',
      String(groupIndex)
    );

    if (!key) {
      group.appendChild(
        drawerLink(
          label,
          'drawer-trigger'
        )
      );

      return group;
    }

    const data =
      menuData[key];

    if (!data) {
      return group;
    }

    const trigger =
      document.createElement(
        'button'
      );

    trigger.className =
      'drawer-trigger';

    trigger.type =
      'button';

    const submenuId =
      `drawer-submenu-${key}`;

    trigger.setAttribute(
      'aria-expanded',
      'false'
    );

    trigger.setAttribute(
      'aria-controls',
      submenuId
    );

    const labelElement =
      document.createElement(
        'span'
      );

    labelElement.textContent =
      label;

    const arrowShell =
      document.createElement(
        'span'
      );

    arrowShell.className =
      'drawer-arrow-shell';

    arrowShell.setAttribute(
      'aria-hidden',
      'true'
    );

    const arrowSvg =
      document.createElementNS(
        'http://www.w3.org/2000/svg',
        'svg'
      );

    arrowSvg.setAttribute(
      'class',
      'icon'
    );

    const arrowUse =
      document.createElementNS(
        'http://www.w3.org/2000/svg',
        'use'
      );

    arrowUse.setAttribute(
      'href',
      '#i-chevron'
    );

    arrowSvg.appendChild(
      arrowUse
    );

    arrowShell.appendChild(
      arrowSvg
    );

    trigger.append(
      labelElement,
      arrowShell
    );

    const submenu =
      document.createElement(
        'div'
      );

    submenu.className =
      'drawer-submenu';

    submenu.id =
      submenuId;

    const inner =
      document.createElement(
        'div'
      );

    inner.className =
      'drawer-submenu-inner';

    data.items.forEach(
      (item, index) => {
        const link =
          drawerLink(
            item.title
          );

        link.style.setProperty(
          '--drawer-link-index',
          String(index)
        );

        inner.appendChild(
          link
        );
      }
    );

    submenu.appendChild(
      inner
    );

    trigger.addEventListener(
      'click',
      () => {
        const opening =
          !group.classList
            .contains(
              'is-open'
            );

        closeOtherDrawerGroups(
          group
        );

        group.classList.toggle(
          'is-open',
          opening
        );

        trigger.setAttribute(
          'aria-expanded',
          opening
            ? 'true'
            : 'false'
        );
      }
    );

    trigger.addEventListener(
      'keydown',
      event => {
        if (
          event.key
          === 'ArrowDown'
        ) {
          event.preventDefault();

          if (
            !group.classList
              .contains(
                'is-open'
              )
          ) {
            closeOtherDrawerGroups(
              group
            );

            group.classList.add(
              'is-open'
            );

            trigger.setAttribute(
              'aria-expanded',
              'true'
            );
          }

          qs(
            'a',
            submenu
          )?.focus();

          return;
        }

        if (
          event.key
          === 'Escape'
        ) {
          event.preventDefault();

          group.classList.remove(
            'is-open'
          );

          trigger.setAttribute(
            'aria-expanded',
            'false'
          );

          trigger.focus();
        }
      }
    );

    group.append(
      trigger,
      submenu
    );

    return group;
  }


  /*
  |--------------------------------------------------------------------------
  | Mobile Information Architecture
  |--------------------------------------------------------------------------
  */

  function buildDrawer() {
    if (!drawerNav) {
      return;
    }

    const groups = [
      [
        'Home',
        null
      ],

      [
        'About Us',
        'about'
      ],

      [
        'Solutions',
        'solutions'
      ],

      [
        'Products',
        'products'
      ],

      [
        'Resources',
        'resources'
      ],

      [
        'Contact',
        'contact'
      ]
    ];

    drawerNav.replaceChildren(
      ...groups.map(
        (
          [label, key],
          index
        ) =>
          makeDrawerGroup(
            label,
            key,
            index
          )
      )
    );
  }


  buildDrawer();


  /*
  |--------------------------------------------------------------------------
  | Mobile Drawer Link Handling
  |--------------------------------------------------------------------------
  */

  drawerNav?.addEventListener(
    'click',
    event => {
      const link =
        event.target.closest(
          'a'
        );

      if (!link) {
        return;
      }

      if (
        link.matches(
          '[data-search-link]'
        )
      ) {
        event.preventDefault();

        qs('#closeVhiDrawer')
          ?.click();

        window.setTimeout(
          () => {
            qs('#openSearch')
              ?.click();
          },
          REDUCED_MOTION
            ? 0
            : 120
        );

        return;
      }

      const href =
        link.getAttribute(
          'href'
        )
        || '';

      if (
        href.startsWith('#')
      ) {
        event.preventDefault();

        /*
         * Let app.js close the drawer using its
         * established lifecycle.
         */
        qs('#closeVhiDrawer')
          ?.click();

        window.setTimeout(
          () => {
            navigateHash(
              href
            );
          },
          REDUCED_MOTION
            ? 0
            : 150
        );
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | Active Section / Navigation Awareness
  |--------------------------------------------------------------------------
  |
  | Adds subtle intelligence to the header without changing URLs.
  |
  */

  const sectionMap = [
    {
      selector:
        '#hero',

      menu:
        null
    },

    {
      selector:
        '#challenges',

      menu:
        'about'
    },

    {
      selector:
        '#categories',

      menu:
        'products'
    },

    {
      selector:
        '#featured',

      menu:
        'products'
    },

    {
      selector:
        '#procedure',

      menu:
        'solutions'
    },

    {
      selector:
        '#organizations',

      menu:
        'about'
    },

    {
      selector:
        '#why',

      menu:
        'about'
    },

    {
      selector:
        '#resources',

      menu:
        'resources'
    },

    {
      selector:
        '#opportunities',

      menu:
        'contact'
    },

    {
      selector:
        '#partners',

      menu:
        'about'
    },

    {
      selector:
        '#faq',

      menu:
        'resources'
    },

    {
      selector:
        '#cta',

      menu:
        'contact'
    }
  ];


  function clearNavigationCurrentState() {
    navigationBubbles.forEach(
      bubble => {
        bubble.classList.remove(
          'is-current'
        );

        if (
          bubble.matches(
            'a[href="#hero"]'
          )
        ) {
          bubble.removeAttribute(
            'aria-current'
          );
        }
      }
    );
  }


  function markCurrentMenu(
    key
  ) {
    clearNavigationCurrentState();

    if (!key) {
      const home =
        qs(
          '.nav-bubble[href="#hero"]'
        );

      home?.classList.add(
        'is-current'
      );

      home?.setAttribute(
        'aria-current',
        'page'
      );

      return;
    }

    const trigger =
      triggerForKey(
        key
      );

    trigger?.classList.add(
      'is-current'
    );
  }


  if (
    'IntersectionObserver'
    in window
  ) {
    const observedSections =
      sectionMap
        .map(item => ({
          ...item,
          element:
            qs(
              item.selector
            )
        }))
        .filter(item =>
          Boolean(
            item.element
          )
        );

    if (
      observedSections.length
    ) {
      const sectionObserver =
        new IntersectionObserver(
          entries => {
            const visible =
              entries
                .filter(
                  entry =>
                    entry.isIntersecting
                )
                .sort(
                  (a, b) =>
                    b.intersectionRatio
                    - a.intersectionRatio
                );

            if (
              !visible.length
            ) {
              return;
            }

            const current =
              observedSections
                .find(
                  item =>
                    item.element
                    === visible[0].target
                );

            if (current) {
              markCurrentMenu(
                current.menu
              );
            }
          },
          {
            rootMargin:
              '-20% 0px -60% 0px',

            threshold: [
              0.01,
              0.15,
              0.35,
              0.6
            ]
          }
        );

      observedSections.forEach(
        item => {
          sectionObserver.observe(
            item.element
          );
        }
      );
    }
  }


  /*
  |--------------------------------------------------------------------------
  | Responsive State
  |--------------------------------------------------------------------------
  */

  function handleResponsiveState() {
    resizeFrame = 0;

    const mobile =
      window.innerWidth
      <= MOBILE_BREAKPOINT;

    document.body.classList.toggle(
      'vsl-mobile-header',
      mobile
    );

    document.body.classList.toggle(
      'vsl-desktop-header',
      !mobile
    );

    if (mobile) {
      closeMega({
        force: true
      });
    }
  }


  window.addEventListener(
    'resize',
    () => {
      if (resizeFrame) {
        return;
      }

      resizeFrame =
        window.requestAnimationFrame(
          handleResponsiveState
        );
    },
    {
      passive: true
    }
  );


  handleResponsiveState();


  /*
  |--------------------------------------------------------------------------
  | Initial Header State
  |--------------------------------------------------------------------------
  */

  document.documentElement
    .classList.add(
      'vsl-header-js'
    );

  headerIsland?.classList.add(
    'vsl-premium-header'
  );


  /*
  |--------------------------------------------------------------------------
  | Cleanup
  |--------------------------------------------------------------------------
  */

  window.addEventListener(
    'pagehide',
    () => {
      window.clearTimeout(
        closeTimer
      );

      closeTimer = 0;

      window.clearTimeout(
        headerRevealTimer
      );

      headerRevealTimer = 0;

      window.clearTimeout(
        headerFallbackTimer
      );

      headerFallbackTimer = 0;

      if (typingFrame) {
        window.cancelAnimationFrame(
          typingFrame
        );

        typingFrame = 0;
      }

      if (resizeFrame) {
        window.cancelAnimationFrame(
          resizeFrame
        );

        resizeFrame = 0;
      }

      if (scrollFrame) {
        window.cancelAnimationFrame(
          scrollFrame
        );

        scrollFrame = 0;
      }
    },
    {
      once: true
    }
  );
})();
