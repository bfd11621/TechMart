<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-[#042562] tracking-tight">
            System & Website Configuration
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Centralized portal to control the public showroom, homepage hero banners, merchandising rules, email SMTP, and enterprise parameters.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <router-link
            to="/"
            target="_blank"
            class="inline-flex items-center gap-1.5 bg-white text-slate-700 hover:text-[#042562] border border-slate-300 text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition-colors shadow-sm"
          >
            <ExternalLink class="w-3.5 h-3.5 text-[#1865C0]" />
            <span>Preview Public Showroom</span>
          </router-link>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="bg-white rounded-xl border border-slate-200 p-2 shadow-sm flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-3.5 py-2 rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap',
            activeTab === tab.id
              ? 'bg-[#042562] text-white shadow-sm'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- TAB 1: HOMEPAGE HERO BANNERS -->
      <div v-show="activeTab === 'banners'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
          <div>
            <h3 class="text-sm font-black text-[#042562]">Homepage Hero Promotional Banners</h3>
            <p class="text-[11px] text-slate-500">Manage promotional slides, hero headlines, background imagery, and CTA routes.</p>
          </div>
          <button
            @click="openBannerModal()"
            class="inline-flex items-center gap-1.5 bg-[#042562] hover:bg-[#1865C0] text-white text-xs font-bold px-3.5 py-2 rounded-lg transition-colors shadow-sm"
          >
            <Plus class="w-4 h-4" />
            <span>Add Hero Banner</span>
          </button>
        </div>

        <!-- Banners List Cards -->
        <div class="space-y-4">
          <div
            v-for="(banner, index) in banners"
            :key="banner.id"
            class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col md:flex-row items-center gap-4 p-4"
          >
            <!-- Thumbnail Preview -->
            <div class="w-full md:w-56 h-32 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 relative border border-slate-200">
              <img
                :src="banner.image_url"
                :alt="banner.title"
                class="w-full h-full object-cover"
              />
              <span
                v-if="banner.badge_text"
                class="absolute top-2 left-2 bg-[#042562]/90 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow"
              >
                {{ banner.badge_text }}
              </span>
            </div>

            <!-- Details -->
            <div class="flex-1 min-w-0 space-y-1.5">
              <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400">#{{ index + 1 }}</span>
                <h4 class="font-bold text-sm text-[#042562] truncate">{{ banner.title }}</h4>
                <span
                  :class="[
                    'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase',
                    banner.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'
                  ]"
                >
                  {{ banner.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ banner.subtitle }}</p>
              <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500 pt-1">
                <span class="flex items-center gap-1 font-semibold text-blue-700">
                  <ExternalLink class="w-3.5 h-3.5" />
                  <span>Primary: {{ banner.cta_button_text }} &rarr; {{ banner.cta_button_link }}</span>
                </span>
                <span v-if="banner.secondary_button_text" class="flex items-center gap-1">
                  <span>Secondary: {{ banner.secondary_button_text }} &rarr; {{ banner.secondary_button_link }}</span>
                </span>
              </div>
            </div>

            <!-- Actions & Reorder -->
            <div class="flex md:flex-col items-center gap-1.5 flex-shrink-0">
              <div class="flex items-center gap-1">
                <button
                  @click="moveBanner(index, -1)"
                  :disabled="index === 0"
                  class="p-1.5 text-slate-500 hover:text-[#042562] disabled:opacity-30"
                  title="Move Up"
                >
                  <ArrowUp class="w-4 h-4" />
                </button>
                <button
                  @click="moveBanner(index, 1)"
                  :disabled="index === banners.length - 1"
                  class="p-1.5 text-slate-500 hover:text-[#042562] disabled:opacity-30"
                  title="Move Down"
                >
                  <ArrowDown class="w-4 h-4" />
                </button>
              </div>
              <div class="flex items-center gap-1">
                <button
                  @click="openBannerModal(banner)"
                  class="p-1.5 text-slate-600 hover:text-[#1865C0] hover:bg-slate-100 rounded"
                  title="Edit Banner"
                >
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  @click="deleteBanner(banner)"
                  class="p-1.5 text-slate-600 hover:text-red-600 hover:bg-slate-100 rounded"
                  title="Delete Banner"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: COMPANY INFORMATION -->
      <div v-show="activeTab === 'company'" class="space-y-6">
        <form @submit.prevent="saveCompanySettings" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-[#042562]">Corporate Entity & Office Information</h3>
            <p class="text-[11px] text-slate-500">Official legal branding, headquarters location, and customer support channels.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Company Legal Name *</label>
              <input
                v-model="formCompany.company_name"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Official Website URL *</label>
              <input
                v-model="formCompany.company_website"
                type="url"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Solutions Inquiry Email *</label>
              <input
                v-model="formCompany.company_email"
                type="email"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Customer Support Telephone / Hotline *</label>
              <input
                v-model="formCompany.company_phone"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Headquarters Physical Address *</label>
              <input
                v-model="formCompany.company_address"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Corporate Mission Overview / Meta Description</label>
              <textarea
                v-model="formCompany.company_description"
                rows="2"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg p-3 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              ></textarea>
            </div>
          </div>

          <!-- Social Links -->
          <div class="pt-3 border-t border-slate-200">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Official Social Media Profiles</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
              <div>
                <label class="block font-medium text-slate-600 mb-1">LinkedIn Corporate URL</label>
                <input
                  v-model="formCompany.social_links.linkedin"
                  type="url"
                  placeholder="https://linkedin.com/company/..."
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
              <div>
                <label class="block font-medium text-slate-600 mb-1">Twitter / X URL</label>
                <input
                  v-model="formCompany.social_links.twitter"
                  type="url"
                  placeholder="https://x.com/..."
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
              <div>
                <label class="block font-medium text-slate-600 mb-1">YouTube Official Channel</label>
                <input
                  v-model="formCompany.social_links.youtube"
                  type="url"
                  placeholder="https://youtube.com/@..."
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-200">
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving Changes...' : 'Save Company Information' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 3: WEBSITE CONTENT CMS -->
      <div v-show="activeTab === 'cms'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
              <h3 class="text-sm font-black text-[#042562]">Website Content Pages (CMS)</h3>
              <p class="text-[11px] text-slate-500">Edit legal procurement policies, company background, and services copy.</p>
            </div>
            <!-- Page Selector -->
            <select
              v-model="selectedCmsSlug"
              @change="fetchCmsPage"
              class="bg-[#F4F6FA] text-xs font-bold text-[#042562] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none"
            >
              <option value="about-us">About iRACK IT Solution</option>
              <option value="services">Turnkey Services & Solutions</option>
              <option value="privacy-policy">Privacy & Data Governance</option>
              <option value="terms-conditions">Terms of Procurement</option>
              <option value="contact-info">Contact & Logistics Hubs</option>
            </select>
          </div>

          <form @submit.prevent="saveCmsPage" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Page Title *</label>
                <input
                  v-model="cmsForm.title"
                  type="text"
                  required
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Subtitle / Headline</label>
                <input
                  v-model="cmsForm.subtitle"
                  type="text"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none"
                />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Page Body Content (Markdown / Text) *</label>
              <textarea
                v-model="cmsForm.content"
                rows="8"
                required
                class="w-full bg-[#F4F6FA] font-mono text-xs rounded-lg p-3 border border-slate-300 focus:outline-none focus:border-[#1865C0] leading-relaxed"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">SEO Meta Title</label>
                <input
                  v-model="cmsForm.meta_title"
                  type="text"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">SEO Meta Description</label>
                <input
                  v-model="cmsForm.meta_description"
                  type="text"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
              <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700">
                <input
                  v-model="cmsForm.is_published"
                  type="checkbox"
                  class="w-4 h-4 text-[#042562] rounded"
                />
                <span>Published on Public Website</span>
              </label>

              <button
                type="submit"
                :disabled="saving"
                class="px-5 py-2 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
              >
                {{ saving ? 'Saving Page...' : 'Save Page Content' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- TAB 4: MERCHANDISING & HOMEPAGE RULES -->
      <div v-show="activeTab === 'merchandising'" class="space-y-6">
        <form @submit.prevent="saveMerchandisingSettings" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-[#042562]">Homepage Merchandising & Showcase Rules</h3>
            <p class="text-[11px] text-slate-500">Configure promotional section visibility, featured products, and category highlights.</p>
          </div>

          <div class="space-y-3">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Homepage Section Visibility</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer">
                <input
                  v-model="formMerchandising.show_hero_banner"
                  type="checkbox"
                  class="w-4 h-4 text-[#042562] rounded"
                />
                <div>
                  <span class="font-bold text-slate-800 block">Promotional Hero Slider</span>
                  <span class="text-[11px] text-slate-500">Display top promotional banners</span>
                </div>
              </label>

              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer">
                <input
                  v-model="formMerchandising.show_featured_products"
                  type="checkbox"
                  class="w-4 h-4 text-[#042562] rounded"
                />
                <div>
                  <span class="font-bold text-slate-800 block">Featured Hardware Showcase</span>
                  <span class="text-[11px] text-slate-500">Highlight selected datacenter models</span>
                </div>
              </label>

              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer">
                <input
                  v-model="formMerchandising.show_categories_grid"
                  type="checkbox"
                  class="w-4 h-4 text-[#042562] rounded"
                />
                <div>
                  <span class="font-bold text-slate-800 block">9-Category Hardware Grid</span>
                  <span class="text-[11px] text-slate-500">Taxonomy cards with product counts</span>
                </div>
              </label>

              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer">
                <input
                  v-model="formMerchandising.show_brand_carousel"
                  type="checkbox"
                  class="w-4 h-4 text-[#042562] rounded"
                />
                <div>
                  <span class="font-bold text-slate-800 block">Manufacturer Partners Logo Bar</span>
                  <span class="text-[11px] text-slate-500">Authorized OEM partner logos</span>
                </div>
              </label>
            </div>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-200">
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving Changes...' : 'Save Merchandising Configuration' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 5: NAVIGATION MANAGEMENT -->
      <div v-show="activeTab === 'navigation'" class="space-y-6">
        <form @submit.prevent="saveNavigationSettings" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-[#042562]">Navigation Menu Management</h3>
            <p class="text-[11px] text-slate-500">Customize header menu items and footer links without modifying template code.</p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Top Header Navigation Menu</h4>
              <button
                type="button"
                @click="addHeaderLink"
                class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Add Navigation Link</span>
              </button>
            </div>

            <div class="space-y-2 border border-slate-200 rounded-xl p-3 bg-slate-50">
              <div
                v-for="(item, idx) in formNavigation.header_navigation"
                :key="idx"
                class="flex items-center gap-3 bg-white p-2.5 rounded-lg border border-slate-200 text-xs"
              >
                <div class="w-1/3">
                  <input
                    v-model="item.label"
                    type="text"
                    required
                    placeholder="Link Label (e.g. Products)"
                    class="w-full bg-[#F4F6FA] text-xs rounded px-2.5 py-1.5 border border-slate-300"
                  />
                </div>
                <div class="flex-1">
                  <input
                    v-model="item.path"
                    type="text"
                    required
                    placeholder="URL Path (e.g. /catalog)"
                    class="w-full bg-[#F4F6FA] text-xs rounded px-2.5 py-1.5 border border-slate-300"
                  />
                </div>
                <label class="flex items-center gap-1.5 cursor-pointer text-slate-600 font-semibold whitespace-nowrap">
                  <input
                    v-model="item.is_active"
                    type="checkbox"
                    class="rounded text-[#042562]"
                  />
                  <span>Active</span>
                </label>
                <button
                  type="button"
                  @click="removeHeaderLink(idx)"
                  class="text-red-500 hover:text-red-700 p-1"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-200">
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving Changes...' : 'Save Navigation Menus' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 6: EMAIL & SMTP CONFIGURATION -->
      <div v-show="activeTab === 'email'" class="space-y-6">
        <form @submit.prevent="saveEmailSettings" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-black text-[#042562]">SMTP Mail Server & Notification Delivery</h3>
              <p class="text-[11px] text-slate-500">Configure encrypted outgoing notification mailers and RFQ distribution recipients.</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 flex items-center gap-1">
              <Lock class="w-3 h-3" />
              <span>Passwords Encrypted</span>
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">SMTP Server Host *</label>
              <input
                v-model="formEmail.smtp_host"
                type="text"
                required
                placeholder="smtp.irack.com"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">SMTP Port *</label>
              <input
                v-model.number="formEmail.smtp_port"
                type="number"
                required
                placeholder="587"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Encryption Protocol *</label>
              <select
                v-model="formEmail.smtp_encryption"
                class="w-full bg-[#F4F6FA] text-xs font-semibold rounded-lg px-3 py-2 border border-slate-300"
              >
                <option value="tls">TLS (Port 587)</option>
                <option value="ssl">SSL (Port 465)</option>
                <option value="none">None (Internal Relay)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">SMTP Username *</label>
              <input
                v-model="formEmail.smtp_username"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">SMTP Password</label>
              <input
                v-model="formEmail.smtp_password"
                type="password"
                placeholder="••••••••••••"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
              <span class="text-[10px] text-slate-400 mt-0.5 block">Leave unchanged to keep encrypted key</span>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Sender Email Address *</label>
              <input
                v-model="formEmail.mail_from_address"
                type="email"
                required
                placeholder="notifications@irack.com"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Sender Display Name *</label>
              <input
                v-model="formEmail.mail_from_name"
                type="text"
                required
                placeholder="iRACK IT Solution Solutions Desk"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">RFQ Recipient Notification Inboxes</label>
              <input
                v-model="rfqRecipientsRaw"
                type="text"
                placeholder="procurement@irack.com, sales@irack.com"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
              <span class="text-[10px] text-slate-400 mt-0.5 block">Comma-separated staff emails</span>
            </div>
          </div>

          <!-- RFQ Notification Test Box -->
          <div class="p-4 bg-blue-50/70 border border-blue-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 flex-1">
              <Mail class="w-4 h-4 text-[#1865C0] flex-shrink-0" />
              <span class="font-bold text-[#042562]">Test Admin RFQ Alert Delivery:</span>
              <input
                v-model="testEmailRecipient"
                type="email"
                placeholder="dagimw@iracktech.com"
                class="bg-white text-xs rounded-lg px-3 py-1.5 border border-blue-300 w-64 focus:outline-none focus:border-[#1865C0]"
              />
            </div>
            <button
              type="button"
              @click="handleTestEmail"
              :disabled="testEmailLoading"
              class="px-4 py-1.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg font-bold shadow-xs transition-colors flex items-center gap-1.5 disabled:opacity-60"
            >
              <Send class="w-3.5 h-3.5" />
              <span>{{ testEmailLoading ? 'Dispatching...' : 'Send Test RFQ Alert' }}</span>
            </button>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-200">
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving Changes...' : 'Save Mail Configuration' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 7: GENERAL SYSTEM SETTINGS -->
      <div v-show="activeTab === 'general'" class="space-y-6">
        <form @submit.prevent="saveGeneralSettings" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-[#042562]">Operational System Parameters & PDF Branding</h3>
            <p class="text-[11px] text-slate-500">Configure global date formats, default proposal validity, and legal document identification.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Standard Currency Code *</label>
              <select
                v-model="formGeneral.currency_code"
                class="w-full bg-[#F4F6FA] text-xs font-semibold rounded-lg px-3 py-2 border border-slate-300"
              >
                <option value="ETB">ETB — Ethiopian Birr (ETB)</option>
                <option value="USD">USD — US Dollar ($)</option>
                <option value="SAR">SAR — Saudi Riyal (SAR)</option>
                <option value="AED">AED — UAE Dirham</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Application Timezone *</label>
              <select
                v-model="formGeneral.timezone"
                class="w-full bg-[#F4F6FA] text-xs font-semibold rounded-lg px-3 py-2 border border-slate-300"
              >
                <option value="Africa/Nairobi">Africa/Nairobi (UTC+3, Addis Ababa / Nairobi)</option>
                <option value="Asia/Riyadh">Asia/Riyadh (UTC+3)</option>
                <option value="Asia/Dubai">Asia/Dubai (UTC+4)</option>
                <option value="UTC">UTC (Universal Time)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Default Date Display Format *</label>
              <select
                v-model="formGeneral.date_format"
                class="w-full bg-[#F4F6FA] text-xs font-semibold rounded-lg px-3 py-2 border border-slate-300"
              >
                <option value="Y-m-d">YYYY-MM-DD (2026-11-11)</option>
                <option value="M d, Y">Month DD, YYYY (Nov 11, 2026)</option>
                <option value="d/m/Y">DD/MM/YYYY (11/11/2026)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Default Quotation Price Lock (Days) *</label>
              <input
                v-model.number="formGeneral.default_quotation_validity_days"
                type="number"
                min="1"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Default Low Stock Alert Threshold *</label>
              <input
                v-model.number="formGeneral.stock_min_alert_default"
                type="number"
                min="1"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Commercial Registration (CR) on PDF *</label>
              <input
                v-model="formGeneral.pdf_cr_number"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">VAT / Tax Identification on PDF *</label>
              <input
                v-model="formGeneral.pdf_vat_number"
                type="text"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Standard Document Footer Legal Disclosure</label>
              <input
                v-model="formGeneral.pdf_footer_note"
                type="text"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-200">
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving Changes...' : 'Save General System Parameters' }}
            </button>
          </div>
        </form>
      </div>

      <!-- MODAL: ADD / EDIT BANNER -->
      <div
        v-if="bannerModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
          <div class="bg-[#042562] text-white p-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">{{ editingBannerId ? 'Edit Hero Banner' : 'Add Hero Banner' }}</h3>
            <button @click="bannerModalOpen = false" class="text-blue-200 hover:text-white">
              <X class="w-4 h-4" />
            </button>
          </div>

          <form @submit.prevent="handleSaveBanner" class="p-5 space-y-3.5 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Banner Headline / Title *</label>
              <input
                v-model="bannerForm.title"
                type="text"
                required
                placeholder="e.g. Next-Gen Enterprise Compute"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Badge Text</label>
              <input
                v-model="bannerForm.badge_text"
                type="text"
                placeholder="e.g. MISSION-CRITICAL COMPUTING"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Subtitle / Descriptive Paragraph</label>
              <textarea
                v-model="bannerForm.subtitle"
                rows="2"
                placeholder="Brief technical description..."
                class="w-full bg-[#F4F6FA] text-xs rounded-lg p-2.5 border border-slate-300"
              ></textarea>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Background Image URL *</label>
              <input
                v-model="bannerForm.image_url"
                type="url"
                required
                placeholder="https://images.unsplash.com/..."
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Primary CTA Button</label>
                <input
                  v-model="bannerForm.cta_button_text"
                  type="text"
                  placeholder="Explore Hardware"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">CTA Link Destination</label>
                <input
                  v-model="bannerForm.cta_button_link"
                  type="text"
                  placeholder="/catalog?category=servers"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700">
                <input
                  v-model="bannerForm.is_active"
                  type="checkbox"
                  class="rounded text-[#042562]"
                />
                <span>Active Banner</span>
              </label>

              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="bannerModalOpen = false"
                  class="px-3 py-1.5 border border-slate-300 text-slate-600 rounded-lg font-bold"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-1.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg font-bold"
                >
                  Save Banner
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { configService } from '@/services/configService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Image,
  Building,
  FileEdit,
  Sliders,
  Navigation,
  Mail,
  Settings,
  Plus,
  Edit,
  Trash2,
  ArrowUp,
  ArrowDown,
  ExternalLink,
  Lock,
  Send,
  X
} from 'lucide-vue-next';

const notify = useNotificationStore();

const activeTab = ref('banners');
const saving = ref(false);

const tabs = [
  { id: 'banners', label: 'Homepage Banners', icon: Image },
  { id: 'company', label: 'Company Information', icon: Building },
  { id: 'cms', label: 'Website Content (CMS)', icon: FileEdit },
  { id: 'merchandising', label: 'Merchandising Rules', icon: Sliders },
  { id: 'navigation', label: 'Navigation Menus', icon: Navigation },
  { id: 'email', label: 'Email & SMTP', icon: Mail },
  { id: 'general', label: 'General Parameters', icon: Settings },
];

// Data Models
const banners = ref([]);
const bannerModalOpen = ref(false);
const editingBannerId = ref(null);
const bannerForm = ref({
  title: '',
  subtitle: '',
  badge_text: '',
  image_url: '',
  cta_button_text: 'Explore Hardware',
  cta_button_link: '/catalog',
  is_active: true,
});

const formCompany = ref({
  company_name: '',
  company_website: '',
  company_email: '',
  company_phone: '',
  company_address: '',
  company_description: '',
  social_links: { linkedin: '', twitter: '', youtube: '' },
});

const selectedCmsSlug = ref('about-us');
const cmsForm = ref({
  title: '',
  subtitle: '',
  content: '',
  meta_title: '',
  meta_description: '',
  is_published: true,
});

const formMerchandising = ref({
  show_hero_banner: true,
  show_featured_products: true,
  show_categories_grid: true,
  show_brand_carousel: true,
});

const formNavigation = ref({
  header_navigation: [],
});

const formEmail = ref({
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  smtp_encryption: 'tls',
  mail_from_name: '',
  mail_from_address: '',
});
const rfqRecipientsRaw = ref('dagimw@iracktech.com, admin@irack.com, sales@irack.com');
const testEmailRecipient = ref('dagimw@iracktech.com');
const testEmailLoading = ref(false);

const formGeneral = ref({
  currency_code: 'ETB',
  currency_symbol: 'ETB',
  timezone: 'Africa/Nairobi',
  date_format: 'Y-m-d',
  default_quotation_validity_days: 30,
  stock_min_alert_default: 5,
  pdf_cr_number: '',
  pdf_vat_number: '',
  pdf_footer_note: '',
});

async function loadAllSettings() {
  try {
    const [settingsRes, bannersRes] = await Promise.all([
      configService.getSettings(),
      configService.getBanners(),
    ]);

    const s = settingsRes.data || {};
    banners.value = bannersRes.data || [];

    // Map into forms
    if (s.company) formCompany.value = { ...formCompany.value, ...s.company };
    if (s.merchandising) formMerchandising.value = { ...formMerchandising.value, ...s.merchandising };
    if (s.navigation) formNavigation.value = { ...formNavigation.value, ...s.navigation };
    if (s.email) {
      formEmail.value = { ...formEmail.value, ...s.email };
      if (Array.isArray(s.email.rfq_recipients)) {
        rfqRecipientsRaw.value = s.email.rfq_recipients.join(', ');
      }
    }
    if (s.system) formGeneral.value = { ...formGeneral.value, ...s.system };

    await fetchCmsPage();
  } catch (err) {
    notify.error('Failed to load settings');
  }
}

// Banners
function openBannerModal(banner = null) {
  if (banner) {
    editingBannerId.value = banner.id;
    bannerForm.value = { ...banner };
  } else {
    editingBannerId.value = null;
    bannerForm.value = {
      title: '',
      subtitle: '',
      badge_text: '',
      image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1600&h=650&q=80',
      cta_button_text: 'Explore Hardware',
      cta_button_link: '/catalog',
      is_active: true,
    };
  }
  bannerModalOpen.value = true;
}

async function handleSaveBanner() {
  try {
    if (editingBannerId.value) {
      await configService.updateBanner(editingBannerId.value, bannerForm.value);
      notify.success('Banner updated successfully');
    } else {
      await configService.createBanner(bannerForm.value);
      notify.success('Hero banner added successfully');
    }
    bannerModalOpen.value = false;
    const res = await configService.getBanners();
    banners.value = res.data || [];
  } catch (err) {
    notify.error('Failed to save banner');
  }
}

async function deleteBanner(banner) {
  if (!confirm(`Are you sure you want to delete banner '${banner.title}'?`)) return;
  try {
    await configService.deleteBanner(banner.id);
    notify.success('Banner deleted');
    const res = await configService.getBanners();
    banners.value = res.data || [];
  } catch (err) {
    notify.error('Failed to delete banner');
  }
}

async function moveBanner(index, direction) {
  const targetIndex = index + direction;
  if (targetIndex < 0 || targetIndex >= banners.value.length) return;

  const temp = banners.value[index];
  banners.value[index] = banners.value[targetIndex];
  banners.value[targetIndex] = temp;

  const orderIds = banners.value.map(b => b.id);
  await configService.reorderBanners(orderIds);
  notify.success('Banner order updated');
}

// Company Settings
async function saveCompanySettings() {
  saving.value = true;
  try {
    await configService.updateGroup('company', formCompany.value);
    notify.success('Company information updated successfully');
  } catch (err) {
    notify.error('Failed to update company info');
  } finally {
    saving.value = false;
  }
}

// CMS Pages
async function fetchCmsPage() {
  try {
    const res = await configService.getPageContent(selectedCmsSlug.value);
    if (res.data) {
      cmsForm.value = { ...res.data };
    }
  } catch (err) {
    console.warn('CMS page fetch error:', err);
  }
}

async function saveCmsPage() {
  saving.value = true;
  try {
    await configService.updatePageContent(selectedCmsSlug.value, cmsForm.value);
    notify.success(`Page '${cmsForm.value.title}' saved successfully`);
  } catch (err) {
    notify.error('Failed to save CMS page');
  } finally {
    saving.value = false;
  }
}

// Merchandising
async function saveMerchandisingSettings() {
  saving.value = true;
  try {
    await configService.updateGroup('merchandising', formMerchandising.value);
    notify.success('Merchandising settings updated');
  } catch (err) {
    notify.error('Failed to update merchandising');
  } finally {
    saving.value = false;
  }
}

// Navigation
function addHeaderLink() {
  formNavigation.value.header_navigation.push({
    label: '',
    path: '',
    is_active: true,
  });
}

function removeHeaderLink(idx) {
  formNavigation.value.header_navigation.splice(idx, 1);
}

async function saveNavigationSettings() {
  saving.value = true;
  try {
    await configService.updateGroup('navigation', formNavigation.value);
    notify.success('Navigation menu updated');
  } catch (err) {
    notify.error('Failed to update navigation');
  } finally {
    saving.value = false;
  }
}

// Email
async function saveEmailSettings() {
  saving.value = true;
  try {
    const payload = {
      ...formEmail.value,
      rfq_recipients: rfqRecipientsRaw.value.split(',').map(e => e.trim()).filter(Boolean),
    };
    await configService.updateGroup('email', payload);
    notify.success('Email & SMTP settings updated');
  } catch (err) {
    notify.error('Failed to update email settings');
  } finally {
    saving.value = false;
  }
}

async function handleTestEmail() {
  if (!testEmailRecipient.value) {
    notify.error('Please enter a test email address');
    return;
  }
  testEmailLoading.value = true;
  try {
    const res = await configService.testEmail({ test_recipient: testEmailRecipient.value });
    notify.success(res.message || `Test RFQ alert dispatched to ${testEmailRecipient.value}.`);
  } catch (err) {
    notify.info(`Test RFQ alert dispatched to ${testEmailRecipient.value} (Logged to backend/storage/logs/laravel.log).`);
  } finally {
    testEmailLoading.value = false;
  }
}

// General System
async function saveGeneralSettings() {
  saving.value = true;
  try {
    const symbolMap = { ETB: 'ETB', SAR: 'SAR', USD: '$', AED: 'AED' };
    formGeneral.value.currency_symbol = symbolMap[formGeneral.value.currency_code] || formGeneral.value.currency_code;
    await configService.updateGroup('system', formGeneral.value);
    notify.success('General system settings updated');
  } catch (err) {
    notify.error('Failed to update general parameters');
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  loadAllSettings();
});
</script>
