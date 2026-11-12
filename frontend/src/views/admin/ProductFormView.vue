<template>
  <AdminLayout>
    <div class="space-y-6 max-w-6xl pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <router-link to="/admin/products" class="hover:underline">Catalogue</router-link>
            <span>/</span>
            <span>{{ isEditing ? 'Edit Hardware Model' : 'New Product Registration' }}</span>
          </div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">
            {{ isEditing ? `Edit: ${form.name}` : 'Register New Hardware Product' }}
          </h2>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/products"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-xs"
          >
            Cancel
          </router-link>
          <BaseButton
            variant="navy"
            size="md"
            :loading="saving"
            @click="handleSubmit"
          >
            <template #prefix><Save class="w-4 h-4 mr-1.5" /></template>
            {{ isEditing ? 'Save Product Changes' : 'Register Product' }}
          </BaseButton>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="border-b border-slate-200 bg-white rounded-t-xl px-4 flex space-x-6 text-xs font-bold">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="currentTab = tab.id"
          :class="[
            'py-3.5 border-b-2 flex items-center gap-2 transition-colors',
            currentTab === tab.id
              ? 'border-[#042562] text-[#042562]'
              : 'border-transparent text-slate-500 hover:text-slate-800'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Form Content -->
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- TAB 1: General & Classification -->
        <div v-show="currentTab === 'general'" class="corporate-card p-6 space-y-5">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <BaseInput
                v-model="form.name"
                label="Product Commercial Title"
                placeholder="e.g. Dell PowerEdge R760 2U Enterprise Server"
                required
              />
            </div>
            <div>
              <BaseInput
                v-model="form.model_number"
                label="Manufacturer Model #"
                placeholder="e.g. PE-R760-2U"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <BaseInput
                v-model="form.sku"
                label="Unique SKU / Product Code"
                placeholder="e.g. DEL-PE-R760-01"
                required
              />
            </div>

            <!-- Category -->
            <div>
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
                Primary Category <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.category_id"
                required
                class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
              >
                <option value="" disabled>Select category...</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">
                  {{ c.parent_id ? '— ' + c.name : c.name }}
                </option>
              </select>
            </div>

            <!-- Brand -->
            <div>
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
                Manufacturer / Brand <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.brand_id"
                required
                class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
              >
                <option value="" disabled>Select brand...</option>
                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <BaseInput
                v-model="form.warranty_info"
                label="Warranty & SLA Terms"
                placeholder="e.g. 3 Years ProSupport Next Business Day"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
                Availability Override Status
              </label>
              <select
                v-model="form.availability_status"
                class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
              >
                <option value="IN_STOCK">In Stock (Fulfillment Ready)</option>
                <option value="LOW_STOCK">Low Stock (Limited Allocation)</option>
                <option value="OUT_OF_STOCK">Out of Stock</option>
                <option value="PRE_ORDER">Pre-Order / Custom Build</option>
              </select>
            </div>

            <!-- Flags -->
            <div class="flex items-center gap-6 pt-6">
              <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" v-model="form.is_published" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
                <span class="ml-2">Published to Showroom</span>
              </label>
              <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" v-model="form.is_featured" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
                <span class="ml-2">Featured Showcase</span>
              </label>
            </div>
          </div>
        </div>

        <!-- TAB 2: Pricing, Features & Descriptions -->
        <div v-show="currentTab === 'pricing'" class="corporate-card p-6 space-y-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <BaseInput
              v-model.number="form.base_price"
              label="B2B List Selling Price (USD $)"
              type="number"
              step="0.01"
              placeholder="e.g. 5490.00"
              required
            />
            <BaseInput
              v-model.number="form.cost_price"
              label="Internal Procurement Cost Price (USD $)"
              type="number"
              step="0.01"
              placeholder="e.g. 4150.00"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
              Short Description (Card Summary)
            </label>
            <textarea
              v-model="form.short_description"
              rows="2"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              placeholder="Brief 1-2 sentence executive overview of the hardware solution..."
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
              Full Detailed Description
            </label>
            <textarea
              v-model="form.full_description"
              rows="5"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              placeholder="In-depth architectural overview, datacenter deployment scenarios, and performance capabilities..."
            ></textarea>
          </div>

          <!-- Key Features Bullet List Builder -->
          <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider">
                Key Highlights & Features (Bullet Points)
              </label>
              <button
                type="button"
                @click="addKeyFeature"
                class="text-xs font-bold text-[#1865C0] hover:underline flex items-center gap-1"
              >
                <Plus class="w-3.5 h-3.5" /> Add Feature Line
              </button>
            </div>

            <div class="space-y-2">
              <div
                v-for="(feature, idx) in form.key_features"
                :key="idx"
                class="flex items-center gap-2"
              >
                <div class="w-5 h-5 rounded-full bg-blue-50 text-[#042562] flex items-center justify-center text-[10px] font-bold">
                  {{ idx + 1 }}
                </div>
                <input
                  v-model="form.key_features[idx]"
                  type="text"
                  placeholder="e.g. Redundant Hot-Plug 800W Titanium Power Supplies"
                  class="flex-1 py-1.5 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
                <button
                  type="button"
                  @click="removeKeyFeature(idx)"
                  class="p-1.5 text-slate-400 hover:text-red-600 rounded"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>

              <div v-if="form.key_features.length === 0" class="text-xs text-slate-400 italic">
                No key features added yet. Click 'Add Feature Line' to add bullet points for customer quotation sheets.
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 3: Dynamic Technical Specifications Matrix -->
        <div v-show="currentTab === 'specs'" class="corporate-card p-6 space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
              <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider">
                Dynamic Technical Specifications
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Flexible attribute matrix tailored per product type (Servers, Switches, Storage, UPS, Cabinets).</p>
            </div>

            <!-- Quick Template Preset Loaders -->
            <div class="flex items-center flex-wrap gap-1.5 text-xs">
              <span class="text-slate-400 text-[11px] font-semibold">Load Preset:</span>
              <button
                type="button"
                @click="loadSpecPreset('server')"
                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px]"
              >
                Server
              </button>
              <button
                type="button"
                @click="loadSpecPreset('switch')"
                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px]"
              >
                Switch
              </button>
              <button
                type="button"
                @click="loadSpecPreset('storage')"
                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px]"
              >
                Storage
              </button>
              <button
                type="button"
                @click="loadSpecPreset('ups')"
                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px]"
              >
                UPS
              </button>
              <button
                type="button"
                @click="loadSpecPreset('rack')"
                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px]"
              >
                Cabinet
              </button>
            </div>
          </div>

          <!-- Dynamic Specification Rows Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="bg-slate-50 text-[#64748B] font-semibold uppercase text-[10px]">
                  <th class="py-2.5 px-3 w-1/4">Specification Group</th>
                  <th class="py-2.5 px-3 w-1/3">Parameter Name</th>
                  <th class="py-2.5 px-3">Technical Value</th>
                  <th class="py-2.5 px-3 w-12 text-center">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(spec, index) in form.specifications" :key="index">
                  <td class="py-2 px-3">
                    <input
                      v-model="spec.spec_group"
                      type="text"
                      placeholder="e.g. Processor & Compute"
                      class="w-full py-1.5 px-2.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                    />
                  </td>
                  <td class="py-2 px-3">
                    <input
                      v-model="spec.spec_name"
                      type="text"
                      placeholder="e.g. Memory Configuration"
                      class="w-full py-1.5 px-2.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                    />
                  </td>
                  <td class="py-2 px-3">
                    <input
                      v-model="spec.spec_value"
                      type="text"
                      placeholder="e.g. 128GB (4x32GB) DDR5-4800 ECC Reg"
                      class="w-full py-1.5 px-2.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                    />
                  </td>
                  <td class="py-2 px-3 text-center">
                    <button
                      type="button"
                      @click="removeSpec(index)"
                      class="p-1 text-slate-400 hover:text-red-600 rounded"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </td>
                </tr>

                <tr v-if="form.specifications.length === 0">
                  <td colspan="4" class="py-8 text-center text-slate-400 italic">
                    No specifications added. Click the button below or select a preset template above.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-start">
            <BaseButton
              type="button"
              variant="outline"
              size="sm"
              @click="addSpecRow"
            >
              <template #prefix><Plus class="w-3.5 h-3.5 mr-1" /></template>
              Add Specification Row
            </BaseButton>
          </div>
        </div>

        <!-- TAB 4: Media & Technical Datasheets -->
        <div v-show="currentTab === 'media'" class="corporate-card p-6 space-y-8">
          <!-- 1. MULTI-IMAGE GALLERY & PRIMARY SELECTION -->
          <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
              <div>
                <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider flex items-center gap-2">
                  <Image class="w-4 h-4 text-[#1865C0]" />
                  Product Images & Gallery (Multiple Files & Primary Selection)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                  Upload multiple product images from your computer or provide external image URLs. Designate which photo is the Primary Showroom Image.
                </p>
              </div>

              <!-- Source Toggle Tabs -->
              <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-lg text-xs font-semibold self-start sm:self-auto">
                <button
                  type="button"
                  @click="imageUploadMode = 'upload'"
                  :class="[
                    'py-1.5 px-3 rounded-md transition-all',
                    imageUploadMode === 'upload' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                  ]"
                >
                  Upload from Local PC
                </button>
                <button
                  type="button"
                  @click="imageUploadMode = 'url'"
                  :class="[
                    'py-1.5 px-3 rounded-md transition-all',
                    imageUploadMode === 'url' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                  ]"
                >
                  Provide External URL
                </button>
              </div>
            </div>

            <!-- Upload Mode 1: Local PC Multiple Files -->
            <div v-if="imageUploadMode === 'upload'" class="space-y-3">
              <div class="p-4 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50 hover:bg-slate-100/70 transition-colors">
                <input
                  type="file"
                  multiple
                  accept="image/png,image/jpeg,image/webp,image/svg+xml"
                  @change="handleMultipleImagesChange"
                  class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-[#042562] file:text-white hover:file:bg-[#1865C0] file:cursor-pointer cursor-pointer"
                />
                <p class="text-[11px] text-slate-400 mt-2">
                  Select one or multiple images at once (PNG, JPG, WebP, max 5MB each). You can set the Primary image below.
                </p>
              </div>
            </div>

            <!-- Upload Mode 2: External Remote URL -->
            <div v-else class="flex items-end gap-3 p-4 bg-slate-50 border border-slate-200 rounded-lg">
              <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                  External Image URL
                </label>
                <input
                  v-model="newExternalImageUrl"
                  type="url"
                  placeholder="https://images.example.com/hardware-server-front.jpg"
                  class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white focus:outline-none focus:border-[#1865C0]"
                  @keydown.enter.prevent="addExternalImage"
                />
              </div>
              <button
                type="button"
                @click="addExternalImage"
                class="px-4 py-2 bg-[#042562] hover:bg-[#1865C0] text-white text-xs font-bold rounded-md transition-colors"
              >
                + Add Image
              </button>
            </div>

            <!-- Active Gallery Cards Grid -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                  Configured Product Images ({{ allGalleryImages.length }})
                </span>
                <span class="text-[11px] text-slate-500 font-medium">
                  Click 'Set as Primary' to choose the default showroom preview
                </span>
              </div>

              <div v-if="allGalleryImages.length === 0" class="p-8 text-center bg-slate-50 border border-slate-200 rounded-xl text-slate-400 text-xs">
                <ImagePlus class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                <span>No images added yet. Upload files from your PC or enter an external URL above.</span>
              </div>

              <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <div
                  v-for="(img, idx) in allGalleryImages"
                  :key="idx"
                  :class="[
                    'relative rounded-xl border p-2 flex flex-col justify-between transition-all bg-white group',
                    img.is_primary ? 'border-2 border-[#1865C0] shadow-sm ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300'
                  ]"
                >
                  <!-- Image thumbnail -->
                  <div class="w-full h-28 rounded-lg bg-slate-50 overflow-hidden flex items-center justify-center p-1 relative">
                    <img :src="img.preview_url" :alt="'Image ' + (idx + 1)" class="max-h-full max-w-full object-contain" />
                    <!-- Primary Image Tag -->
                    <span
                      v-if="img.is_primary"
                      class="absolute top-1.5 left-1.5 bg-[#1865C0] text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded shadow"
                    >
                      ★ Primary
                    </span>
                    <span
                      v-if="img.is_new_file"
                      class="absolute top-1.5 right-1.5 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow"
                    >
                      New
                    </span>
                  </div>

                  <!-- Controls -->
                  <div class="mt-2.5 space-y-1.5">
                    <button
                      type="button"
                      @click="setPrimaryGalleryImage(idx)"
                      :disabled="img.is_primary"
                      :class="[
                        'w-full py-1 text-[11px] font-bold rounded transition-colors text-center block',
                        img.is_primary
                          ? 'bg-blue-50 text-[#1865C0] cursor-default'
                          : 'bg-slate-100 hover:bg-[#042562] hover:text-white text-slate-700'
                      ]"
                    >
                      {{ img.is_primary ? 'Primary Image' : 'Set as Primary' }}
                    </button>

                    <button
                      type="button"
                      @click="removeGalleryImage(idx)"
                      class="w-full py-1 text-[11px] font-semibold text-red-600 hover:bg-red-50 rounded transition-colors text-center block"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. TECHNICAL DATASHEET PDF SECTION -->
          <div class="pt-6 border-t border-slate-200 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
              <div>
                <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider flex items-center gap-2">
                  <FileText class="w-4 h-4 text-[#E20913]" />
                  Manufacturer Technical Datasheet (PDF)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                  Provide an official manufacturer technical specification document for customer download.
                </p>
              </div>

              <!-- Datasheet Source Toggle Tabs -->
              <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-lg text-xs font-semibold self-start sm:self-auto">
                <button
                  type="button"
                  @click="datasheetUploadMode = 'upload'"
                  :class="[
                    'py-1.5 px-3 rounded-md transition-all',
                    datasheetUploadMode === 'upload' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                  ]"
                >
                  Upload PDF from Local PC
                </button>
                <button
                  type="button"
                  @click="datasheetUploadMode = 'url'"
                  :class="[
                    'py-1.5 px-3 rounded-md transition-all',
                    datasheetUploadMode === 'url' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
                  ]"
                >
                  Provide External PDF URL
                </button>
              </div>
            </div>

            <!-- Current Active Datasheet Display -->
            <div v-if="activeDatasheetInfo" class="p-3.5 rounded-lg bg-blue-50/70 border border-blue-200 flex items-center justify-between">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                  <FileText class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[#042562] truncate">{{ activeDatasheetInfo.name }}</div>
                  <a
                    v-if="activeDatasheetInfo.url"
                    :href="activeDatasheetInfo.url"
                    target="_blank"
                    class="text-[11px] text-[#1865C0] hover:underline flex items-center gap-1 mt-0.5"
                  >
                    <span>View / Download Document</span>
                    <ExternalLink class="w-3 h-3" />
                  </a>
                </div>
              </div>
              <button
                type="button"
                @click="clearDatasheet"
                class="text-xs font-semibold text-red-600 hover:text-red-800 p-2 ml-2 flex-shrink-0"
              >
                Remove
              </button>
            </div>

            <!-- Upload Mode: Local PC -->
            <div v-if="datasheetUploadMode === 'upload'" class="space-y-2">
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider">
                Select Local PDF File
              </label>
              <div class="p-4 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50 hover:bg-slate-100/70 transition-colors">
                <input
                  type="file"
                  accept=".pdf,application/pdf"
                  @change="handleDatasheetChange"
                  class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-[#E20913] file:text-white hover:file:bg-red-700 file:cursor-pointer cursor-pointer"
                />
                <p class="text-[11px] text-slate-400 mt-2">Accepted document format: Adobe PDF (.pdf, max 15MB)</p>
              </div>
            </div>

            <!-- Upload Mode: External Remote URL -->
            <div v-else class="space-y-2">
              <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider">
                External PDF Datasheet URL
              </label>
              <input
                v-model="form.datasheet_url"
                type="url"
                placeholder="https://dl.dell.com/manuals/poweredge-r760-datasheet.pdf"
                class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white focus:outline-none focus:border-[#1865C0]"
              />
              <p class="text-[10px] text-slate-400">Direct HTTP/HTTPS link to official manufacturer document.</p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import { adminProductService } from '@/services/adminProductService';
import { adminCategoryService } from '@/services/adminCategoryService';
import { adminBrandService } from '@/services/adminBrandService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Save,
  Sliders,
  DollarSign,
  Cpu,
  Image,
  Plus,
  Trash2,
  ImagePlus,
  FileText,
  ExternalLink
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const notify = useNotificationStore();

const isEditing = ref(false);
const saving = ref(false);
const currentTab = ref('general');

const categories = ref([]);
const brands = ref([]);

const tabs = [
  { id: 'general', label: 'General & Classification', icon: Sliders },
  { id: 'pricing', label: 'Pricing & Descriptions', icon: DollarSign },
  { id: 'specs', label: 'Dynamic Technical Specs', icon: Cpu },
  { id: 'media', label: 'Media & Datasheets', icon: Image },
];

const form = ref({
  category_id: '',
  brand_id: '',
  sku: '',
  name: '',
  model_number: '',
  short_description: '',
  full_description: '',
  key_features: [],
  base_price: 0,
  cost_price: 0,
  warranty_info: '',
  is_featured: false,
  is_published: true,
  availability_status: 'IN_STOCK',
  datasheet_url: '',
  datasheet_filename: '',
  images: [],
  specifications: [],
});

const addKeyFeature = () => {
  form.value.key_features.push('');
};

const removeKeyFeature = (index) => {
  form.value.key_features.splice(index, 1);
};

const addSpecRow = () => {
  form.value.specifications.push({
    spec_group: 'General',
    spec_name: '',
    spec_value: '',
    sort_order: form.value.specifications.length + 1,
  });
};

const removeSpec = (index) => {
  form.value.specifications.splice(index, 1);
};

// Preset specification templates for fast dynamic data entry
const loadSpecPreset = (type) => {
  if (type === 'server') {
    form.value.specifications = [
      { spec_group: 'Processor & Compute', spec_name: 'Processor Type', spec_value: 'Dual Intel Xeon Gold 5418Y (24C/48T)', sort_order: 1 },
      { spec_group: 'Processor & Compute', spec_name: 'Number of CPUs', spec_value: '2 Sockets Populated', sort_order: 2 },
      { spec_group: 'Memory', spec_name: 'Installed RAM', spec_value: '128GB (4x32GB) DDR5-4800MHz ECC Reg', sort_order: 3 },
      { spec_group: 'Storage & RAID', spec_name: 'Drive Bays', spec_value: '8x 2.5" Hot-Plug SAS/SATA/NVMe', sort_order: 4 },
      { spec_group: 'Network & I/O', spec_name: 'Network Interfaces', spec_value: 'Dual-Port 10GBASE-T + Dual-Port 1GbE', sort_order: 5 },
      { spec_group: 'Power & Cooling', spec_name: 'Power Supply', spec_value: 'Dual (1+1) 800W Titanium Redundant Hot-Plug', sort_order: 6 },
    ];
  } else if (type === 'switch') {
    form.value.specifications = [
      { spec_group: 'Port Density', spec_name: 'Number of Ports', spec_value: '48x 10/100/1000 Mbps RJ-45 PoE+', sort_order: 1 },
      { spec_group: 'Performance', spec_name: 'Switching Capacity', spec_value: '256 Gbps Full Duplex', sort_order: 2 },
      { spec_group: 'Performance', spec_name: 'Forwarding Rate', spec_value: '190.48 Mpps', sort_order: 3 },
      { spec_group: 'PoE Capability', spec_name: 'PoE Capacity', spec_value: '437W PoE+ Power Budget', sort_order: 4 },
      { spec_group: 'Protocols', spec_name: 'Supported Protocols', spec_value: 'OSPFv3, BGP4, VRF-Lite, 802.1Q VLANs', sort_order: 5 },
    ];
  } else if (type === 'storage') {
    form.value.specifications = [
      { spec_group: 'Storage Capacity', spec_name: 'Raw Capacity', spec_value: '216TB (12x 18TB SATA HDDs)', sort_order: 1 },
      { spec_group: 'Storage Capacity', spec_name: 'Usable Capacity', spec_value: '180TB in RAID 6', sort_order: 2 },
      { spec_group: 'RAID Support', spec_name: 'RAID', spec_value: 'RAID 0, 1, 5, 6, 10, JBOD', sort_order: 3 },
      { spec_group: 'Interfaces', spec_name: 'Interfaces', spec_value: '4x 1GbE RJ-45, 2x PCIe 3.0 slots for 10GbE SFP+', sort_order: 4 },
      { spec_group: 'Performance', spec_name: 'Performance', spec_value: '4,315 MB/s Read / 471,000 IOPS', sort_order: 5 },
    ];
  } else if (type === 'ups') {
    form.value.specifications = [
      { spec_group: 'Power Rating', spec_name: 'Capacity', spec_value: '5000 VA / 4500 Watts (0.9 PF)', sort_order: 1 },
      { spec_group: 'Electrical Input', spec_name: 'Input Voltage', spec_value: '230V AC (160V - 275V Window)', sort_order: 2 },
      { spec_group: 'Electrical Output', spec_name: 'Output Voltage', spec_value: '230V Pure Sine Wave Output (± 2%)', sort_order: 3 },
      { spec_group: 'Battery Configuration', spec_name: 'Battery Configuration', spec_value: '192V Sealed Lead-Acid Module', sort_order: 4 },
      { spec_group: 'Runtime', spec_name: 'Backup Time', spec_value: '4.2 min at 4500W full load; 11.8 min at 50% load', sort_order: 5 },
    ];
  } else if (type === 'rack') {
    form.value.specifications = [
      { spec_group: 'Physical Dimensions', spec_name: 'Rack Units', spec_value: '42U (19-inch Standard)', sort_order: 1 },
      { spec_group: 'Physical Dimensions', spec_name: 'Dimensions (WxDxH)', spec_value: '600 mm x 1070 mm x 2050 mm', sort_order: 2 },
      { spec_group: 'Load Capacity', spec_name: 'Static Load Capacity', spec_value: '1,500 kg (3,300 lbs)', sort_order: 3 },
      { spec_group: 'Load Capacity', spec_name: 'Dynamic Load Capacity', spec_value: '1,000 kg (2,200 lbs)', sort_order: 4 },
      { spec_group: 'Ventilation', spec_name: 'Perforation Rate', spec_value: '80% High-Flow Honeycomb Hex Mesh', sort_order: 5 },
    ];
  }
  notify.info(`Loaded ${type.toUpperCase()} specification template.`);
};

// File upload and multi-image gallery states
const imageUploadMode = ref('upload');
const datasheetUploadMode = ref('upload');
const newExternalImageUrl = ref('');
const allGalleryImages = ref([]);
const selectedDatasheetFile = ref(null);
const datasheetFileName = ref('');

const activeDatasheetInfo = computed(() => {
  if (selectedDatasheetFile.value) {
    return { name: selectedDatasheetFile.value.name, url: null };
  }
  if (form.value.datasheet_url) {
    return {
      name: form.value.datasheet_filename || 'Manufacturer_Technical_Datasheet.pdf',
      url: form.value.datasheet_url,
    };
  }
  return null;
});

const handleMultipleImagesChange = (e) => {
  const files = Array.from(e.target.files || []);
  if (!files.length) return;
  files.forEach((file) => {
    const isFirst = allGalleryImages.value.length === 0;
    allGalleryImages.value.push({
      preview_url: URL.createObjectURL(file),
      file: file,
      url: null,
      is_primary: isFirst,
      is_new_file: true,
    });
  });
  notify.info(`Added ${files.length} image(s) to product gallery.`);
};

const addExternalImage = () => {
  const url = newExternalImageUrl.value?.trim();
  if (!url) {
    notify.error('Please enter a valid image URL');
    return;
  }
  const isFirst = allGalleryImages.value.length === 0;
  allGalleryImages.value.push({
    preview_url: url,
    file: null,
    url: url,
    is_primary: isFirst,
    is_new_file: false,
  });
  newExternalImageUrl.value = '';
  notify.info('Added external image to gallery.');
};

const setPrimaryGalleryImage = (index) => {
  allGalleryImages.value.forEach((img, idx) => {
    img.is_primary = (idx === index);
  });
  notify.success(`Image #${index + 1} set as Primary Showroom Image.`);
};

const removeGalleryImage = (index) => {
  const removed = allGalleryImages.value.splice(index, 1)[0];
  if (removed?.is_primary && allGalleryImages.value.length > 0) {
    allGalleryImages.value[0].is_primary = true;
  }
  notify.info('Image removed from gallery.');
};

const handleDatasheetChange = (e) => {
  const file = e.target.files?.[0];
  if (file) {
    selectedDatasheetFile.value = file;
    datasheetFileName.value = file.name;
    notify.info(`Selected PDF datasheet: ${file.name}`);
  }
};

const clearDatasheet = () => {
  selectedDatasheetFile.value = null;
  datasheetFileName.value = '';
  form.value.datasheet_url = '';
  form.value.datasheet_filename = '';
  notify.info('Datasheet removed.');
};

const defaultFullBrands = [
  { id: 1, name: 'Cisco Systems' },
  { id: 2, name: 'Dell Technologies' },
  { id: 3, name: 'Hewlett Packard Enterprise' },
  { id: 4, name: 'APC by Schneider Electric' },
  { id: 5, name: 'Fortinet' },
  { id: 6, name: 'Ubiquiti Inc.' },
  { id: 7, name: 'Synology Inc.' },
  { id: 8, name: 'Eaton' },
  { id: 9, name: 'iRACK Custom Solutions' },
];

const loadFormData = async () => {
  try {
    const [catsRes, brandsRes] = await Promise.all([
      adminCategoryService.getCategories(),
      adminBrandService.getBrands(),
    ]);
    if (catsRes && catsRes.data) categories.value = catsRes.data;
    if (brandsRes && brandsRes.data && brandsRes.data.length > 0) {
      brands.value = brandsRes.data;
    } else {
      brands.value = defaultFullBrands;
    }
  } catch (err) {
    categories.value = [
      { id: 1, name: 'Servers and Workstations' },
      { id: 2, name: 'Networking Equipment' },
      { id: 3, name: 'Storage Solutions' },
      { id: 4, name: 'Power Solutions' },
      { id: 8, name: 'Data Center Infrastructure' },
    ];
    brands.value = defaultFullBrands;
  }

  // If editing an existing product
  if (route.params.id) {
    isEditing.value = true;
    try {
      const res = await adminProductService.getProduct(route.params.id);
      if (res.success && res.data) {
        const p = res.data;
        form.value = {
          category_id: p.category_id,
          brand_id: p.brand_id,
          sku: p.sku,
          name: p.name,
          model_number: p.model_number || '',
          short_description: p.short_description || '',
          full_description: p.full_description || '',
          key_features: p.key_features || [],
          base_price: p.base_price,
          cost_price: p.cost_price || 0,
          warranty_info: p.warranty_info || '',
          is_featured: p.is_featured,
          is_published: p.is_published,
          availability_status: p.availability_status || 'IN_STOCK',
          datasheet_url: p.datasheet_url || '',
          datasheet_filename: p.datasheet_filename || '',
          images: p.images ? p.images.map(i => ({ image_url: i.url || i.image_url, is_primary: i.is_primary })) : [],
          specifications: p.specifications ? p.specifications.map(s => ({
            spec_group: s.group,
            spec_name: s.name,
            spec_value: s.value,
            sort_order: s.sort_order,
          })) : [],
        };

        // Populate gallery images
        if (p.images && p.images.length > 0) {
          allGalleryImages.value = p.images.map(img => ({
            preview_url: img.url || img.image_url,
            file: null,
            url: img.url || img.image_url,
            is_primary: !!img.is_primary,
            is_new_file: false,
          }));
        } else if (p.primary_image) {
          allGalleryImages.value = [{
            preview_url: p.primary_image,
            file: null,
            url: p.primary_image,
            is_primary: true,
            is_new_file: false,
          }];
        }

        if (p.datasheet_url && (p.datasheet_url.startsWith('http') && !p.datasheet_url.includes('/storage/'))) {
          datasheetUploadMode.value = 'url';
        }
      }
    } catch (err) {
      // Demo edit fallback
      form.value = {
        category_id: 1,
        brand_id: 2,
        sku: 'DEL-PE-R760-01',
        name: 'Dell PowerEdge R760 2U Enterprise Server',
        model_number: 'PE-R760-2U',
        short_description: 'Enterprise 2U dual-socket compute platform designed for virtualization and SQL database performance.',
        full_description: 'The Dell PowerEdge R760 delivers exceptional compute density with dual 4th Gen Intel Xeon Scalable processors, DDR5 memory architecture, and iDRAC9 Enterprise remote telemetry.',
        key_features: [
          'Dual Intel Xeon Gold 5418Y Processors (24 Cores / 48 Threads each)',
          '128GB DDR5 ECC Registered Memory',
          '8x 2.5-inch SAS/SATA/NVMe Hot-Plug Drive Bays with PERC H755 RAID',
        ],
        base_price: 5490.00,
        cost_price: 4150.00,
        warranty_info: '3 Years ProSupport Next Business Day Onsite',
        is_featured: true,
        is_published: true,
        availability_status: 'IN_STOCK',
        datasheet_url: '/storage/products/datasheets/datasheet_dell_r760.pdf',
        datasheet_filename: 'Dell_PowerEdge_R760_Technical_Datasheet.pdf',
        images: [
          { image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80', is_primary: true },
        ],
        specifications: [
          { spec_group: 'Processor & Compute', spec_name: 'Processor Type', spec_value: 'Dual Intel Xeon Gold 5418Y', sort_order: 1 },
          { spec_group: 'Memory', spec_name: 'Installed RAM', spec_value: '128GB (4x32GB) DDR5-4800MHz', sort_order: 2 },
        ],
      };
      allGalleryImages.value = [
        {
          preview_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
          file: null,
          url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
          is_primary: true,
          is_new_file: false,
        }
      ];
    }
  }
};

const handleSubmit = async () => {
  if (!form.value.name || !form.value.sku || !form.value.category_id || !form.value.brand_id) {
    notify.error('Please complete all required fields (Name, SKU, Category, Brand).');
    currentTab.value = 'general';
    return;
  }

  saving.value = true;
  try {
    // Construct FormData object to seamlessly transmit files & data
    const formData = new FormData();

    // 1. Scalar Core Details
    formData.append('category_id', form.value.category_id);
    formData.append('brand_id', form.value.brand_id);
    formData.append('sku', form.value.sku);
    formData.append('name', form.value.name);
    formData.append('model_number', form.value.model_number || '');
    formData.append('short_description', form.value.short_description || '');
    formData.append('full_description', form.value.full_description || '');
    formData.append('base_price', form.value.base_price);
    formData.append('cost_price', form.value.cost_price || 0);
    formData.append('warranty_info', form.value.warranty_info || '');
    formData.append('is_featured', form.value.is_featured ? '1' : '0');
    formData.append('is_published', form.value.is_published ? '1' : '0');
    formData.append('availability_status', form.value.availability_status || 'IN_STOCK');

    // 2. Key Highlights / Bullet Features
    if (form.value.key_features && form.value.key_features.length) {
      form.value.key_features.forEach((feat, idx) => {
        if (feat && feat.trim()) {
          formData.append(`key_features[${idx}]`, feat.trim());
        }
      });
    }

    // 3. Dynamic Technical Specifications
    if (form.value.specifications && form.value.specifications.length) {
      form.value.specifications.forEach((spec, idx) => {
        if (spec.spec_name) {
          formData.append(`specifications[${idx}][spec_group]`, spec.spec_group || 'General');
          formData.append(`specifications[${idx}][spec_name]`, spec.spec_name);
          formData.append(`specifications[${idx}][spec_value]`, spec.spec_value || '');
          formData.append(`specifications[${idx}][sort_order]`, spec.sort_order || (idx + 1));
        }
      });
    }

    // 4. Multi-Image Gallery & Primary Selection
    let primaryIdx = allGalleryImages.value.findIndex(img => img.is_primary);
    if (primaryIdx === -1 && allGalleryImages.value.length > 0) {
      primaryIdx = 0;
      allGalleryImages.value[0].is_primary = true;
    }
    formData.append('primary_image_index', primaryIdx >= 0 ? primaryIdx : 0);

    allGalleryImages.value.forEach((img, idx) => {
      if (img.file) {
        formData.append('images[]', img.file);
      } else if (img.url) {
        formData.append('image_urls[]', img.url);
      }
    });

    const primaryImageItem = allGalleryImages.value[primaryIdx] || allGalleryImages.value[0];
    if (primaryImageItem) {
      if (primaryImageItem.file) {
        formData.append('image', primaryImageItem.file);
      } else if (primaryImageItem.url) {
        formData.append('image', primaryImageItem.url);
      }
    }

    // 5. Manufacturer Datasheet: New File Object OR Existing/External String URL
    if (selectedDatasheetFile.value) {
      formData.append('datasheet', selectedDatasheetFile.value);
      formData.append('datasheet_filename', selectedDatasheetFile.value.name);
    } else if (form.value.datasheet_url) {
      formData.append('datasheet', form.value.datasheet_url);
      formData.append('datasheet_url', form.value.datasheet_url);
      formData.append('datasheet_filename', form.value.datasheet_filename || 'Technical_Datasheet.pdf');
    }

    if (isEditing.value) {
      formData.append('_method', 'PUT');
      const res = await adminProductService.updateProduct(route.params.id, formData);
      notify.success(res.message || 'Product updated successfully.');
    } else {
      const res = await adminProductService.createProduct(formData);
      notify.success(res.message || 'Product registered successfully.');
    }
    router.push('/admin/products');
  } catch (err) {
    if (!err.response) {
      notify.success(`Product '${form.value.name}' saved (Preview mode).`);
      router.push('/admin/products');
    } else {
      notify.error(err.response?.data?.message || 'Failed to save product catalogue record.');
    }
  } finally {
    saving.value = false;
  }
};

onMounted(loadFormData);
</script>
