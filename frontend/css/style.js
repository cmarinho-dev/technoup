(function () {
    const classMap = {
        menuGuestWrap: 'flex items-center gap-3',
        menuGuestRegister: 'hidden sm:block rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm border border-slate-300 transition-all hover:bg-slate-50',
        menuGuestLogin: 'block rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700',
        menuItem: 'block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors',
        menuItemInner: 'flex items-center gap-2',
        menuItemIcon: 'h-4 w-4 text-slate-400',
        menuWrap: 'relative inline-block text-left',
        menuButton: 'flex items-center gap-2 rounded-full border-none bg-white p-1 px-3 transition-all hover:bg-slate-50 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2',
        menuAvatar: 'flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold',
        menuAvatarIcon: 'h-5 w-5',
        menuLabel: 'text-sm font-medium text-slate-700 hidden sm:block',
        menuChevron: '-mr-1 h-5 w-5 text-slate-400',
        menuDropdown: 'hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-blue-300 ring-opacity-5 overflow-hidden',
        menuSection: 'py-1',
        menuSectionBorder: 'border-t border-slate-100 py-1',
        menuLogout: 'w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors cursor-pointer',
        menuLogoutInner: 'flex items-center gap-2 font-medium',
        menuLogoutIcon: 'h-4 w-4',
        productIcon: 'h-12 w-12 text-slate-300',
        productImage: 'h-[230px] w-full rounded-2xl object-contain',
        productPriceOld: 'text-sm tracking-[0.2em] text-slate-400',
        productPriceWrap: 'flex items-center gap-3',
        productPriceNew: 'text-2xl font-bold tracking-tight text-slate-900',
        productDiscount: 'inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700',
        productPriceLabel: 'text-xs uppercase tracking-[0.2em] text-slate-400',
        productCard: 'flex min-w-[84%] h-[460px] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]',
        productMedia: 'mb-4 flex h-[230px] items-center justify-center rounded-2xl bg-white',
        productBody: 'flex flex-1 flex-col space-y-3 overflow-hidden',
        productMeta: 'flex items-center justify-between gap-3',
        productType: 'rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700',
        productBrand: 'text-xs font-medium text-slate-500',
        productTitle: 'min-h-[56px] text-lg font-semibold text-slate-900',
        productStore: 'mt-1 text-sm text-slate-500',
        productBottom: 'mt-auto',
        productBottomInner: 'space-y-1',
        storeIcon: 'h-12 w-12 text-slate-300',
        storeImage: 'w-full aspect-[4/3] rounded-2xl object-cover',
        storeCard: 'flex min-w-[84%] min-h-[340px] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]',
        storeMedia: 'mb-4 flex aspect-[4/3] items-center justify-center rounded-2xl bg-slate-100',
        storeBody: 'flex flex-1 flex-col space-y-3',
        storeTitle: 'min-h-[56px] text-lg font-semibold text-slate-900',
        storeLocation: 'mt-1 text-sm text-slate-500',
        storeBottom: 'mt-auto flex items-center justify-between',
        storeBadge: 'rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700',
        storeLink: 'text-sm font-semibold text-blue-600 transition hover:text-blue-700',
        departmentCard: 'min-w-[78%] shrink-0 snap-start rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-sm sm:min-w-[260px]',
        departmentIconWrap: 'mb-10 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10',
        departmentIcon: 'h-6 w-6',
        departmentBody: 'space-y-2',
        departmentTitle: 'text-xl font-semibold',
        departmentText: 'text-sm leading-6 text-slate-300',
        carouselHead: 'flex items-center gap-8 mb-3',
        carouselTitle: 'text-3xl font-bold',
        carouselWrap: 'relative mb-8 overflow-x-hidden',
        carouselButtonPrev: 'absolute -left-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex',
        carouselButtonNext: 'absolute -right-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex',
        carouselButtonIcon: 'h-5 w-5',
        carouselTrack: 'flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-8 pt-2 px-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden',
        catalogEmpty: 'col-span-full text-center py-12 border border-gray-300 rounded-2xl',
        catalogEmptyIcon: 'w-16 h-16 text-gray-300 mx-auto mb-3',
        catalogEmptyText: 'text-lg',
        carouselStoreHead: 'flex items-center mb-3',
        carouselStoreWrap: 'relative mb-8',
        carouselStoreTrack: 'flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4 pt-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden',
        tableEmptyAdmin: 'px-4 py-12 text-center text-sm text-slate-500',
        tableRow: 'align-center *:px-4 *:py-4 *:text-sm *:text-slate-700',
        tableCellNowrap: 'whitespace-nowrap',
        tableActionWrap: 'flex gap-2',
        tableActionToggleBase: 'rounded-xl px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2',
        tableActionToggleOn: 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500',
        tableActionToggleOff: 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-600',
        tableActionDelete: 'rounded-xl bg-red-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2',
        tableStatusBase: 'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
        tableStatusOn: 'bg-emerald-50 text-emerald-700',
        tableStatusOff: 'bg-slate-100 text-slate-600',
        tableStrong: 'font-medium text-slate-900',
        storeTableEmpty: 'px-3 py-8 text-center text-slate-400',
        storeActionEdit: 'rounded-xl bg-blue-600 px-3 py-3 text-xs font-semibold text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
        storeActionDelete: 'rounded-xl bg-red-600 px-3 py-3 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2',
        storeActionIcon: 'size-4',
        feedbackSuccess: 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700',
        feedbackError: 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700'
    };

    function cls(name) {
        return classMap[name] || '';
    }

    function addClasses(selector, classes) {
        const tokens = Array.isArray(classes) ? classes : classes.split(/\s+/).filter(Boolean);
        document.querySelectorAll(selector).forEach((element) => {
            element.classList.add(...tokens);
        });
    }

    function setActiveNav(pageName) {
        document.querySelectorAll('header nav a').forEach((link) => {
            if (link.getAttribute('href')?.endsWith(`/${pageName}.html`) || link.getAttribute('href') === `./${pageName}.html` || link.getAttribute('href') === `../../${pageName}.html`) {
                link.classList.add('font-semibold', 'text-blue-600');
            } else {
                link.classList.add('transition', 'hover:text-blue-600');
            }
        });
    }

    function applySharedHeader() {
        if (!document.querySelector('header')) return;
        addClasses('header', 'bg-white shadow-sm border-b border-slate-200');
        addClasses('header > div', 'mx-auto flex h-16 md:mx-12 lg:mx-28 items-center gap-8 px-4 sm:px-6');
        addClasses('header > div > a:first-child', 'block text-blue-600');
        addClasses('header > div > a:first-child svg', 'w-10 h-10');
        addClasses('header > div > div', 'flex flex-1 items-center justify-end md:justify-between');
        addClasses('header nav', 'hidden md:block');
        addClasses('header nav ul', 'flex items-center gap-6 text-sm font-medium text-slate-600');
    }

    function applyPublicFooter() {
        addClasses('footer', 'border-t border-slate-200 bg-white');
        addClasses('footer > div:first-child', 'mx-auto grid gap-10 px-8 py-10 sm:mx-8 md:mx-16 lg:mx-24 lg:grid-cols-[1.3fr_0.7fr_0.7fr_0.9fr]');
        addClasses('footer > div:first-child > div:first-child > div:first-child', 'flex items-center gap-3 text-blue-600');
        addClasses('footer > div:first-child > div:first-child > div:first-child svg', 'h-10 w-10');
        addClasses('footer > div:first-child > div:first-child > div:first-child p:first-child', 'text-lg font-semibold text-slate-900');
        addClasses('footer > div:first-child > div:first-child > div:first-child p:last-child', 'text-sm text-slate-500');
        addClasses('footer > div:first-child > div:first-child > p:last-child', 'mt-4 max-w-md text-sm leading-7 text-slate-600');
        addClasses('footer > div:first-child > div:not(:first-child) h2', 'text-sm font-semibold uppercase tracking-[0.22em] text-slate-400');
        addClasses('footer > div:first-child > div:not(:first-child) div', 'mt-4 flex flex-col gap-3 text-sm text-slate-600');
        addClasses('footer > div:first-child > div:not(:first-child) a', 'transition hover:text-blue-600');
        addClasses('footer > div:last-child', 'border-t border-slate-200 px-8 py-4 text-center text-sm text-slate-500');
    }

    function applySimpleFooter() {
        addClasses('footer', 'border-t border-slate-200 bg-white');
        addClasses('footer > div', 'mx-auto px-8 py-4 text-center text-sm text-slate-500');
    }

    function applyProfileFooter() {
        addClasses('footer', 'border-t border-slate-200 bg-white');
        addClasses('footer > div:first-child', 'mx-auto grid gap-10 px-8 py-10 sm:mx-8 md:mx-16 lg:mx-24 lg:grid-cols-[1.3fr_0.7fr]');
        addClasses('footer > div:first-child > div:first-child p:first-child', 'text-lg font-semibold text-slate-900');
        addClasses('footer > div:first-child > div:first-child p:last-child', 'text-sm text-slate-500');
        addClasses('footer > div:first-child > div:last-child h2', 'text-sm font-semibold uppercase tracking-[0.22em] text-slate-400');
        addClasses('footer > div:first-child > div:last-child div', 'mt-4 flex flex-col gap-3 text-sm text-slate-600');
        addClasses('footer > div:first-child > div:last-child a', 'transition hover:text-blue-600');
        addClasses('footer > div:last-child', 'border-t border-slate-200 px-8 py-4 text-center text-sm text-slate-500');
    }

    function applyHomePage() {
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('main > section:nth-of-type(1)', 'relative overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 px-6 py-8 text-white shadow-xl sm:px-10 sm:py-12');
        addClasses('main > section:nth-of-type(1) > div:first-child', 'absolute inset-y-0 right-0 hidden w-1/2 bg-[radial-gradient(circle_at_top_right,_rgba(96,165,250,0.35),_transparent_55%)] lg:block');
        addClasses('main > section:nth-of-type(1) > div:last-child', 'relative grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center');
        addClasses('main > section:nth-of-type(1) > div:last-child > div', 'space-y-6');
        addClasses('main > section:nth-of-type(1) span', 'inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.28em] text-blue-100');
        addClasses('main > section:nth-of-type(1) h1', 'max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl');
        addClasses('main > section:nth-of-type(1) p', 'max-w-xl text-base leading-7 text-slate-300 sm:text-lg');
        addClasses('main > section:nth-of-type(1) div div:last-child', 'flex flex-wrap gap-3');
        addClasses('main > section:nth-of-type(1) a:first-child', 'rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100');
        addClasses('main > section:nth-of-type(1) a:last-child', 'rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10');
        addClasses('#carousel_departamentos_container', 'mt-10');
        addClasses('#carousel_produtos_container', 'mt-6');
        addClasses('#carousel_lojas_container', 'mt-6');
        addClasses('main > section:last-of-type', 'mt-10 grid gap-4 lg:grid-cols-3');
        addClasses('main > section:last-of-type > article', 'rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm');
        addClasses('main > section:last-of-type > article:nth-child(1) > div:first-child', 'mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600');
        addClasses('main > section:last-of-type > article:nth-child(2) > div:first-child', 'mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600');
        addClasses('main > section:last-of-type > article:nth-child(3) > div:first-child', 'mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600');
        addClasses('main > section:last-of-type > article i', 'h-6 w-6');
        addClasses('main > section:last-of-type > article h2', 'text-xl font-semibold text-slate-900');
        addClasses('main > section:last-of-type > article p', 'mt-3 text-sm leading-7 text-slate-600');
    }

    function applyCatalogPage() {
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('#catalogo_header', 'flex flex-row flex-wrap justify-between items-center mb-3 mt-4');
        addClasses('#catalogo_header > h1', 'text-3xl font-bold mb-2');
        addClasses('#catalogo_header > div', 'flex gap-3 mb-6 flex-wrap');
        addClasses('#catalogo_header > div > div:nth-child(1)', 'relative flex items-center max-w-64');
        addClasses('#filtraTipo', 'box-border flex-1 px-3 pe-10 py-2 border-zinc-200 border rounded-lg max-w-64 hover:border-blue-300 focus:outline-none focus:border-blue-500 appearance-none');
        addClasses('#catalogo_header > div > div:nth-child(1) > i', 'absolute text-zinc-500 bg-white end-0 me-3 z-10 pointer-events-none');
        addClasses('#filtroPrecoMin', 'box-border flex-1 px-3 py-2 border-zinc-200 border rounded-lg max-w-24 hover:border-blue-300 focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none');
        addClasses('#filtroPrecoMax', 'box-border flex-1 px-3 py-2 border-zinc-200 border rounded-lg max-w-24 hover:border-blue-300 focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none');
        addClasses('#catalogo_header > div > div:last-child', 'relative flex items-center max-w-64');
        addClasses('#filtraNome', 'box-border flex-1 px-3 py-2 border-zinc-200 border-2 rounded-lg max-w-64 hover:border-blue-300 focus:outline-none focus:border-blue-500');
        addClasses('#catalogo_header > div > div:last-child > i', 'absolute text-zinc-500 bg-white end-0 me-3 z-10 pointer-events-none');
        addClasses('#catalogo_contador', 'text-gray-700');
        addClasses('#catalogo_items_grid', 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4');
        addClasses('#catalogo_items_grid > p', 'col-span-full text-center text-slate-400 py-8');
    }

    function applyAboutPage() {
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('main > h1:first-child', 'text-2xl font-semibold');
        addClasses('main > p:nth-of-type(1)', 'text-gray-600 text-lg');
        addClasses('main > div:nth-of-type(1)', 'flex items-stretch flex-wrap gap-4 mt-4');
        addClasses('main > div:nth-of-type(1) > div', 'flex flex-col h-full gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm');
        addClasses('main > div:nth-of-type(1) i', 'text-blue-500 size-10');
        addClasses('main > div:nth-of-type(1) h2', 'text-xl font-semibold');
        addClasses('main > div:nth-of-type(1) p', 'text-gray-600');
        addClasses('main > div:nth-of-type(2)', 'my-6');
        addClasses('main > h2:nth-of-type(1)', 'text-xl font-semibold');
        addClasses('main > div:nth-of-type(3)', 'flex items-stretch flex-wrap gap-4');
        addClasses('main > div:nth-of-type(3) > div', 'flex flex-col gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm');
        addClasses('main > div:nth-of-type(3) h3', 'text-xl font-semibold');
        addClasses('main > div:nth-of-type(3) p', 'text-gray-600');
    }

    function applyAuthPage(isReverse) {
        addClasses('body > div:first-child', `${isReverse ? 'flex flex-row-reverse' : 'flex'} min-h-screen w-full bg-white`);
        addClasses('body > div:first-child > div:first-child', 'flex-1 flex flex-col justify-center px-8 lg:flex-none lg:w-1/2 lg:px-16 xl:px-24');
        addClasses('body > div:first-child > div:first-child > div', 'w-full max-w-sm mx-auto');
        addClasses('body > div:first-child > div:first-child > div > div:first-child', 'mb-10 text-blue-600 inline-block');
        addClasses('body > div:first-child > div:first-child > div > div:first-child svg', 'w-12 h-12');
        addClasses('body > div:first-child > div:first-child > div > h1', 'text-3xl font-bold tracking-tight text-slate-900 mb-2');
        addClasses('body > div:first-child > div:first-child > div > p', 'text-sm text-slate-500 mb-8');
        addClasses('body > div:first-child > div:first-child > div > p a', 'font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors');
        addClasses('#mensagemErro', 'hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700');
        addClasses('#loginBox', 'space-y-5');
        addClasses('#registroBox', 'space-y-5');
        addClasses('#loginBox > div label', 'block text-sm font-medium text-slate-700 mb-1.5');
        addClasses('#perfilBox > div label', 'mb-1.5 block text-sm font-medium text-slate-700');
        addClasses('#registroBox > div:first-child > p', 'text-sm font-medium text-slate-700 mb-2');
        addClasses('#registroBox > div:first-child > div', 'flex items-center gap-4');
        addClasses('#registroBox > div:first-child > div > label', 'flex items-center gap-2 cursor-pointer');
        addClasses('#registroBox input[type="radio"]', 'box-border size-3 appearance-none rounded-full ring-1 ring-offset-2 ring-slate-300 checked:ring-blue-600 checked:bg-blue-600');
        addClasses('#registroBox > div:first-child span', 'text-sm text-slate-600');
        addClasses('#loginBox input', 'w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm');
        addClasses('#registroBox input[type="text"], #registroBox input[type="email"], #registroBox input[type="password"]', 'w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm');
        addClasses('#erroSenha', 'hidden mt-1 text-xs text-red-600');
        addClasses('#loginBox > div:nth-last-child(2), #registroBox > div:last-child', 'flex wrap text-center gap-2');
        addClasses('#btnVoltar', 'px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2');
        addClasses('#btnEntrar, #btnCriarConta', 'w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 cursor-pointer');
        addClasses('#teste', 'absolute text-white text-nowrap');
        addClasses('body > div:first-child > div:last-child', 'hidden lg:block lg:flex-1 relative');
        addClasses('body > div:first-child > div:last-child > img', 'absolute inset-0 w-full h-full object-cover');
        addClasses('body > div:first-child > div:last-child > div', 'absolute inset-0 bg-slate-900/5');
    }

    function applyProfilePage() {
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('main > section', 'mx-auto max-w-3xl w-full');
        addClasses('main > section > div:first-child', 'mb-8');
        addClasses('main > section > div:first-child > p:first-child', 'text-sm font-semibold uppercase tracking-[0.22em] text-blue-600');
        addClasses('main > section > div:first-child > h1', 'mt-2 text-3xl font-bold text-slate-900');
        addClasses('#subtitulo', 'mt-3 text-sm leading-7 text-slate-600');
        addClasses('main > section > div:last-child', 'rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8');
        addClasses('#mensagemSucesso', 'hidden mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700');
        addClasses('#mensagemErro', 'hidden mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700');
        addClasses('#perfilBox', 'space-y-5');
        addClasses('#perfilBox input', 'w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600');
        addClasses('#perfilBox > div:last-child', 'flex flex-wrap items-center justify-between gap-3 pt-2');
        addClasses('#btnSalvar', 'rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2');
    }

    function applyAdminPage() {
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('main > div:first-child', 'flex flex-col gap-8 mb-12');
        addClasses('main > div:first-child > div:first-child', 'flex items-center justify-between gap-4 flex-wrap');
        addClasses('main > div:first-child > div:first-child h1', 'text-3xl font-bold');
        addClasses('main > div:first-child > div:first-child p', 'text-sm text-slate-500 mt-2');
        addClasses('#mensagemFeedback', 'hidden rounded-xl border px-4 py-3 text-sm');
        addClasses('main > div:first-child > div:last-child', 'overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm');
        addClasses('main table', 'min-w-full divide-y divide-slate-200');
        addClasses('main thead', 'bg-slate-50 text-left');
        addClasses('main thead tr', '*:px-4 *:py-3 *:text-xs *:font-semibold *:uppercase *:tracking-[0.16em] *:text-slate-500');
        addClasses('main tbody', 'divide-y divide-slate-200');
    }

    function applyStoreFormPage(hasBackButton) {
        addClasses('body > div:first-child', 'flex flex-row-reverse min-h-screen w-full bg-white');
        addClasses('body > div:first-child > div:first-child', 'flex-1 flex flex-col justify-center px-8 lg:flex-none lg:w-1/2 lg:px-16 xl:px-24');
        addClasses('body > div:first-child > div:first-child > div', 'w-full max-w-sm mx-auto');
        addClasses('body > div:first-child > div:first-child > div > div:first-child', 'mb-5 text-blue-600 inline-block');
        addClasses('body > div:first-child > div:first-child > div > div:first-child svg', 'w-12 h-12');
        addClasses('#tituloCadastro', 'mb-5 text-3xl font-bold tracking-tight text-slate-900');
        addClasses('#mensagemFeedback', 'hidden mb-4 rounded-xl border px-4 py-3 text-sm');
        addClasses('#cadastroBox', 'space-y-3');
        addClasses('#cadastroBox > div:first-child', 'space-y-6 py-3 px-2 pe-4 max-h-96 overflow-y-scroll');
        addClasses('#cadastroBox > div:first-child > div', 'relative');
        addClasses('#cadastroBox input', 'peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-sm');
        addClasses('#cadastroBox label', 'absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 text-sm');
        addClasses('#cadastroBox > div:last-child', 'flex wrap text-center gap-2');
        if (hasBackButton) {
            addClasses('#btnVoltar', 'px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2');
        }
        addClasses('#btnSalvarLoja', 'w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 cursor-pointer');
        addClasses('body > div:first-child > div:last-child', 'hidden lg:block lg:flex-1 relative');
        addClasses('body > div:first-child > div:last-child > img', 'absolute inset-0 w-full h-full object-cover');
        addClasses('body > div:first-child > div:last-child > div', 'absolute inset-0 bg-slate-900/5');
    }

    function applyStoreManagePage() {
        applySharedHeader();
        applySimpleFooter();
        addClasses('main', 'flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8');
        addClasses('main > div:first-child', 'flex flex-col gap-8 mb-12');
        addClasses('main > div:first-child > div:first-child', 'flex items-center justify-between');
        addClasses('main > div:first-child > div:first-child > h1', 'text-3xl font-bold');
        addClasses('#btnNovoProduct', 'bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm');
        addClasses('#mensagemFeedback', 'hidden rounded-xl border px-4 py-3 text-sm');
        addClasses('main > div:first-child > div:last-child', 'overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm');
        addClasses('main table', 'min-w-full divide-y divide-slate-200');
        addClasses('main thead', 'bg-slate-50 text-left');
        addClasses('main thead tr', '*:px-4 *:py-3 *:text-xs *:font-semibold *:uppercase *:tracking-[0.16em] *:text-slate-500');
        addClasses('main th', 'px-3 py-2 whitespace-nowrap text-left');
        addClasses('#tabelaProdutosBody', 'divide-y divide-slate-200');
        addClasses('#tabelaProdutosBody > tr:first-child > td', 'px-4 py-8 text-center text-slate-400');
        addClasses('#modalProduto', 'fixed inset-0 z-50 hidden items-center justify-center bg-black/50');
        addClasses('#modalProduto > div', 'bg-white rounded-2xl shadow-xl p-8 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto');
        addClasses('#modalTitulo', 'text-xl font-bold mb-6');
        addClasses('#modalProduto > div > div:first-of-type', 'space-y-4');
        addClasses('#modalProduto > div > div:first-of-type > div', 'relative');
        addClasses('#modalProduto input:not([type="hidden"])', 'peer w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-sm');
        addClasses('#modalProduto label', 'absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 text-sm');
        addClasses('#modalProduto > div > div:last-child', 'flex gap-3 mt-6');
        addClasses('#btnSalvarProduto', 'flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all');
        addClasses('#btnFecharModal', 'flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-all');
    }

    function applyPageStyles() {
        const path = window.location.pathname;

        if (!path.endsWith('/login.html') && !path.endsWith('/registro.html') && !path.endsWith('/protegido/lojas/novo.html') && !path.endsWith('/protegido/lojas/alterar.html')) {
            applySharedHeader();
        }

        if (path.endsWith('/home.html') || path.endsWith('/frontend/') || path.endsWith('/frontend')) {
            setActiveNav('home');
            applyHomePage();
            applyPublicFooter();
            return;
        }

        if (path.endsWith('/catalogo.html')) {
            setActiveNav('catalogo');
            applyCatalogPage();
            applyPublicFooter();
            return;
        }

        if (path.endsWith('/sobre.html')) {
            setActiveNav('sobre');
            applyAboutPage();
            applyPublicFooter();
            return;
        }

        if (path.endsWith('/login.html')) {
            applyAuthPage(false);
            return;
        }

        if (path.endsWith('/registro.html')) {
            applyAuthPage(true);
            return;
        }

        if (path.endsWith('/protegido/conta/perfil.html')) {
            applyProfilePage();
            applyProfileFooter();
            return;
        }

        if (path.endsWith('/protegido/admin/lojas.html') || path.endsWith('/protegido/admin/contas.html')) {
            applyAdminPage();
            applySimpleFooter();
            return;
        }

        if (path.endsWith('/protegido/lojas/novo.html')) {
            applyStoreFormPage(false);
            return;
        }

        if (path.endsWith('/protegido/lojas/alterar.html')) {
            applyStoreFormPage(true);
            return;
        }

        if (path.endsWith('/protegido/lojas/gerenciar_produtos.html')) {
            setActiveNav('');
            applyStoreManagePage();
        }
    }

    window.TechnoUpStyle = {
        cls
    };

    applyPageStyles();
})();
