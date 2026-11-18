<template>
  <div class="flex min-h-screen flex-col bg-[#F4F6FA] text-[#172033]">
    <header class="border-b border-slate-800 bg-[#021840] px-4 py-2 text-xs text-slate-300">
      <div class="mx-auto flex max-w-7xl items-center justify-between">
        <span class="font-medium">Official Enterprise IT Hardware & Datacenter Solutions</span>
        <div class="flex items-center gap-4">
          <a :href="`tel:${company.phone}`" class="font-semibold hover:text-white">{{ company.phone }}</a>
          <a :href="staffPortalUrl" class="font-medium hover:text-white">Staff Portal</a>
        </div>
      </div>
    </header>

    <nav class="sticky top-0 z-40 border-b border-slate-200 bg-white">
      <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4">
        <Link href="/" class="shrink-0">
          <AppLogo variant="dark" size="md" />
        </Link>

        <form class="hidden max-w-md flex-1 md:block" @submit.prevent="search">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="search"
              placeholder="Search server, switch, rack SKU or model..."
              class="w-full rounded-lg border border-slate-300 bg-[#F4F6FA] py-2.5 pl-3 pr-16 text-xs focus:border-[#1865C0] focus:bg-white focus:outline-none"
            />
            <button type="submit" class="absolute right-1.5 top-1.5 rounded bg-[#042562] px-2.5 py-1 text-[11px] font-bold uppercase text-white">
              Go
            </button>
          </div>
        </form>

        <div class="hidden items-center gap-5 text-xs font-bold uppercase tracking-wider xl:flex">
          <Link v-for="item in links" :key="item.href" :href="item.href" :class="linkClass(item.href)">
            {{ item.label }}
          </Link>
        </div>

        <div class="flex items-center gap-2">
          <Link
            v-if="compare.count"
            href="/compare"
            class="relative rounded-lg border border-slate-300 bg-[#F4F6FA] p-2.5 text-[#042562]"
          >
            Compare
            <span class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#1865C0] text-[10px] font-bold text-white">
              {{ compare.count }}
            </span>
          </Link>
          <Link href="/cart" class="flex items-center gap-2 rounded-lg bg-[#042562] px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider text-white">
            Quotation Cart
            <span v-if="cart.uniqueItemCount" class="rounded-full bg-[#E20913] px-1.5 py-0.5 text-[10px]">
              {{ cart.uniqueItemCount }}
            </span>
          </Link>
          <button type="button" class="rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2 text-xs font-bold xl:hidden" @click="open = !open">
            Menu
          </button>
        </div>
      </div>

      <div v-if="open" class="border-t border-slate-200 bg-[#042562] px-4 py-4 text-sm text-white xl:hidden">
        <Link v-for="item in links" :key="item.href" :href="item.href" class="block py-2 font-bold uppercase tracking-wider" @click="open = false">
          {{ item.label }}
        </Link>
        <a :href="staffPortalUrl" class="mt-2 block py-2 text-blue-200">Staff Portal Login</a>
      </div>
    </nav>

    <main class="flex-1">
      <slot />
    </main>

    <footer class="border-t border-blue-900 bg-[#042562] px-4 py-12 text-slate-300">
      <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-4">
        <div class="md:col-span-2">
          <AppLogo variant="light" size="md" />
          <p class="mt-4 max-w-sm text-xs leading-relaxed">
            iRACK IT Solution supplies server racks, enterprise compute, campus switching, storage, and continuous power protection.
          </p>
        </div>
        <div>
          <h2 class="mb-3 text-xs font-black uppercase tracking-wider text-white">Showroom</h2>
          <div class="space-y-2 text-xs">
            <Link href="/catalog" class="block hover:text-white">Shop Catalog</Link>
            <Link href="/categories" class="block hover:text-white">Categories</Link>
            <Link href="/brands" class="block hover:text-white">Brands</Link>
            <Link href="/services" class="block hover:text-white">Solutions</Link>
            <Link href="/cart" class="block hover:text-white">Request a quotation</Link>
          </div>
        </div>
        <div>
          <h2 class="mb-3 text-xs font-black uppercase tracking-wider text-white">Company</h2>
          <div class="space-y-2 text-xs">
            <Link href="/about" class="block hover:text-white">About</Link>
            <Link href="/contact" class="block hover:text-white">Contact</Link>
            <Link href="/privacy" class="block hover:text-white">Privacy</Link>
            <Link href="/terms" class="block hover:text-white">Terms</Link>
            <a :href="staffPortalUrl" class="block hover:text-white">Staff login</a>
          </div>
          <p class="mt-4 text-[11px] text-blue-200">{{ company.email }} · {{ company.phone }}</p>
        </div>
      </div>
      <p class="mx-auto mt-8 max-w-7xl text-xs text-slate-400">© {{ year }} {{ company.name }}. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import { useQuotationCart } from '@/composables/useQuotationCart';
import { useCompare } from '@/composables/useCompare';

const page = usePage();
const company = computed(() => page.props.company || {});
const staffPortalUrl = computed(() => page.props.staffPortalUrl || '/admin/login');
const cart = useQuotationCart();
const compare = useCompare();
const searchQuery = ref('');
const open = ref(false);
const year = new Date().getFullYear();

const links = [
  { href: '/', label: 'Home' },
  { href: '/catalog', label: 'Shop Catalog' },
  { href: '/categories', label: 'Categories' },
  { href: '/brands', label: 'Brands' },
  { href: '/services', label: 'Solutions' },
  { href: '/about', label: 'About' },
  { href: '/contact', label: 'Contact' },
];

function linkClass(href) {
  const current = page.url.split('?')[0];
  const active = href === '/' ? current === '/' : current.startsWith(href);
  return active
    ? 'border-b-2 border-[#E20913] py-2 text-[#042562]'
    : 'border-b-2 border-transparent py-2 text-slate-700 hover:text-[#1865C0]';
}

function search() {
  if (!searchQuery.value.trim()) {
    return;
  }
  open.value = false;
  router.get('/catalog', { search: searchQuery.value.trim() });
}
</script>
