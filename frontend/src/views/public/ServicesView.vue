<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-16 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-3">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Enterprise Services & Solutions</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black">Turnkey IT Engineering & Infrastructure Services</h1>
        <p class="text-sm sm:text-base text-blue-200 mt-2 max-w-2xl leading-relaxed">
          From greenfield datacenter rack fit-outs to enterprise spine-leaf switching architectures, our field engineering teams deliver end-to-end IT integration.
        </p>
      </div>
    </div>

    <!-- Main Solutions Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Solution 1: Datacenter Engineering -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Layers class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">Data Center White Space & Containment</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Complete layout planning for 42U/48U rack rows, hot/cold aisle containment baffles, overhead cable ladders, and raised floor airflow distribution engineered to maximize PUE efficiency.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>CFD Thermal Airflow Simulation</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>1500kg Static Load Seismic Racks</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Modular Overhead Busway Integration</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/catalog?category=data-center-infrastructure"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Browse Enclosure Solutions</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Solution 2: Structured Optical & Copper Cabling -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Cable class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">Structured Fiber & Copper Backbones</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Factory-terminated OM4/OM5 MPO/MTP optical trunk assemblies and Category 6A shielded copper cabling certified with Fluke DTX/DSX test reports.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Ultra High-Density Fiber Cassettes</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Tier 1 & Tier 2 Fluke OTDR Certification</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>25-Year Performance Warranty</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/catalog?category=it-accessories"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Explore Cabling Accessories</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Solution 3: Core Switching & Campus Networking -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Network class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">Enterprise Campus & Spine-Leaf Fabrics</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Scalable multi-gigabit and 100GbE switching fabrics engineered with Cisco Catalyst, Nexus, and Ubiquiti UniFi Pro Max hardware with zero-loss failover.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>BGP EVPN / VXLAN Fabric Design</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>90W UPOE+ High-Density Power</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>StackWise-1T 1Tbps Stacking</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/catalog?category=networking-equipment"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Browse Managed Switches</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Solution 4: Mission-Critical Power & UPS Systems -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Zap class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">Continuous Online Power & UPS Arrays</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Double-conversion online UPS systems (1kVA to 200kVA), automatic transfer switches (ATS), and metered rack PDUs with remote environmental probes.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>N+1 Redundant Battery Topologies</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>SNMP/HTTPS Remote NMC Integration</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Hot-Swappable Power Modules</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/catalog?category=power-solutions"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Browse Online UPS Systems</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Solution 5: Server Compute & Virtualization Clusters -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Server class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">Enterprise Compute & Private Cloud Nodes</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Custom-configured Dell PowerEdge and HPE ProLiant Gen11 dual-socket rackmount servers optimized for VMware vSphere, Proxmox VE, and AI inferencing.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Up to 64-Core Xeon Scalable CPUs</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>PCIe Gen5 High-Throughput NVMe</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Burn-in & Memory Diagnostics Certification</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/catalog?category=servers-and-workstations"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Browse Server Compute</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- Solution 6: 24/7 Managed NOC & Hardware SLAs -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-card-hover transition-all space-y-4 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <ShieldCheck class="w-7 h-7" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">24/7 Rapid Parts Replacement & SLAs</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Contracted response SLAs providing guaranteed 4-hour on-site replacement components directly dispatched from our 3 regional distribution hubs.
            </p>
            <ul class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Dedicated Spares Stock Reserved</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Pre-Configured Advance RMA Swap</span>
              </li>
              <li class="flex items-center gap-2">
                <Check class="w-3.5 h-3.5 text-[#16A34A]" />
                <span>Certified On-Site Field Technicians</span>
              </li>
            </ul>
          </div>
          <router-link
            to="/contact"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 pt-4 border-t border-slate-100"
          >
            <span>Contact SLA Desk</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';
import {
  Layers,
  Cable,
  Network,
  Zap,
  Server,
  ShieldCheck,
  Check,
  ArrowRight,
} from 'lucide-vue-next';
</script>
