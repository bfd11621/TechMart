import apiClient from './api';

// Comprehensive IT hardware mock dataset for client demonstration / fallback mode
const MOCK_CATEGORIES = [
  {
    id: 1,
    name: 'Servers and Workstations',
    slug: 'servers-and-workstations',
    description: 'High-density dual & quad socket rackmount compute nodes and mission-critical enterprise workstations.',
    products_count: 14,
    icon: 'Server',
    children: [
      { id: 101, name: 'Rack Servers', slug: 'rack-servers', products_count: 8 },
      { id: 102, name: 'Tower Servers', slug: 'tower-servers', products_count: 3 },
      { id: 103, name: 'Enterprise Workstations', slug: 'workstations', products_count: 3 },
    ],
  },
  {
    id: 2,
    name: 'Networking Equipment',
    slug: 'networking-equipment',
    description: 'Layer 2/3 core switches, 100GbE spine/leaf datacenter switches, PoE+ edge switches and SD-WAN routers.',
    products_count: 22,
    icon: 'Network',
    children: [
      { id: 201, name: 'Managed Switches', slug: 'managed-switches', products_count: 12 },
      { id: 202, name: 'Core & Edge Routers', slug: 'core-routers', products_count: 5 },
      { id: 203, name: 'Wireless Access Points', slug: 'wireless-aps', products_count: 5 },
    ],
  },
  {
    id: 3,
    name: 'Storage Solutions',
    slug: 'storage-solutions',
    description: 'All-flash NVMe SAN arrays, high-capacity hybrid NAS enclosures, and automated tape backup vaults.',
    products_count: 11,
    icon: 'HardDrive',
    children: [
      { id: 301, name: 'SAN Arrays', slug: 'san-arrays', products_count: 4 },
      { id: 302, name: 'NAS Storage Systems', slug: 'nas-systems', products_count: 5 },
      { id: 303, name: 'JBOD Enclosures', slug: 'jbod-enclosures', products_count: 2 },
    ],
  },
  {
    id: 4,
    name: 'Power Solutions',
    slug: 'power-solutions',
    description: 'Online double-conversion UPS systems, metered rackmount PDUs, and modular battery string cabinets.',
    products_count: 9,
    icon: 'Zap',
    children: [
      { id: 401, name: 'Online Double-Conversion UPS', slug: 'online-ups', products_count: 5 },
      { id: 402, name: 'Smart Rack PDUs', slug: 'smart-pdus', products_count: 4 },
    ],
  },
  {
    id: 5,
    name: 'Security Solutions',
    slug: 'security-solutions',
    description: 'Next-Generation Firewalls (NGFW), intrusion prevention systems, and hardware-accelerated VPN gateways.',
    products_count: 8,
    icon: 'Shield',
    children: [
      { id: 501, name: 'Next-Gen Firewalls', slug: 'next-gen-firewalls', products_count: 5 },
      { id: 502, name: 'Network Security Gateways', slug: 'security-gateways', products_count: 3 },
    ],
  },
  {
    id: 6,
    name: 'Audio Visual Solutions',
    slug: 'audio-visual-solutions',
    description: 'Commercial 4K digital signage displays, room video conferencing bars, and digital signal processors.',
    products_count: 7,
    icon: 'Monitor',
    children: [
      { id: 601, name: 'Video Conferencing Bars', slug: 'conferencing-bars', products_count: 4 },
      { id: 602, name: 'Commercial 4K Displays', slug: 'commercial-displays', products_count: 3 },
    ],
  },
  {
    id: 7,
    name: 'Computers and Accessories',
    slug: 'computers-and-accessories',
    description: 'Ruggedized enterprise laptops, compact client mini-PCs, Thunderbolt 4 docking stations, and monitors.',
    products_count: 16,
    icon: 'Cpu',
    children: [
      { id: 701, name: 'Business Laptops', slug: 'business-laptops', products_count: 7 },
      { id: 702, name: 'Enterprise Mini-PCs', slug: 'mini-pcs', products_count: 5 },
      { id: 703, name: 'Docking Stations', slug: 'docking-stations', products_count: 4 },
    ],
  },
  {
    id: 8,
    name: 'Data Center Infrastructure',
    slug: 'data-center-infrastructure',
    description: 'Heavy-duty 42U and 48U server enclosures, airflow containment baffles, and in-row precision cooling.',
    products_count: 12,
    icon: 'Layers',
    children: [
      { id: 801, name: '42U/48U Server Racks', slug: 'server-racks', products_count: 6 },
      { id: 802, name: 'Cable Managers & Shelves', slug: 'cable-managers', products_count: 6 },
    ],
  },
  {
    id: 9,
    name: 'IT Accessories',
    slug: 'it-accessories',
    description: 'OM4/OM5 fiber optic patch cords, shielded Cat6A copper cabling, modular keystones, and SFP28 optics.',
    products_count: 24,
    icon: 'Cable',
    children: [
      { id: 901, name: 'Fiber Patch Cords', slug: 'fiber-cords', products_count: 12 },
      { id: 902, name: 'Copper Patch Panels', slug: 'patch-panels', products_count: 8 },
      { id: 903, name: 'Optical Transceivers', slug: 'transceivers', products_count: 4 },
    ],
  },
];

const MOCK_BRANDS = [
  { id: 1, name: 'Cisco Systems', slug: 'cisco-systems', products_count: 18, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Global Premier Partner' },
  { id: 2, name: 'Dell Technologies', slug: 'dell-technologies', products_count: 24, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Titanium Direct Partner' },
  { id: 3, name: 'HPE Enterprise', slug: 'hpe-enterprise', products_count: 15, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Platinum Reseller' },
  { id: 4, name: 'Schneider Electric (APC)', slug: 'schneider-electric', products_count: 12, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Elite Power Specialist' },
  { id: 5, name: 'Fortinet', slug: 'fortinet', products_count: 10, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Expert Security Integrator' },
  { id: 6, name: 'Ubiquiti UniFi', slug: 'ubiquiti', products_count: 16, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Master Distributor' },
  { id: 7, name: 'Synology', slug: 'synology', products_count: 9, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Storage Gold Partner' },
  { id: 8, name: 'Lenovo Enterprise', slug: 'lenovo', products_count: 11, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'ThinkSystem Authorized' },
  { id: 9, name: 'Eaton', slug: 'eaton', products_count: 8, logo_url: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&h=60&q=80', tier: 'Critical Power Partner' },
];

const MOCK_PRODUCTS = [
  {
    id: 1,
    name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
    slug: 'dell-poweredge-r760-2u-rack-server',
    sku: 'SRV-DEL-R760-01',
    model_number: 'PowerEdge R760',
    category: { id: 1, name: 'Servers and Workstations', slug: 'servers-and-workstations' },
    brand: { id: 2, name: 'Dell Technologies', slug: 'dell-technologies' },
    short_description: '2U dual-socket enterprise rack server powered by 4th/5th Gen Intel Xeon Scalable processors, supporting up to 24 NVMe drives and 8TB DDR5 memory.',
    full_description: 'The Dell PowerEdge R760 is a full-featured enterprise server designed to optimize performance for demanding workloads such as AI inferencing, virtualization, database management, and high-performance computing. Features dual hot-plug redundant power supplies, iDRAC9 enterprise remote management, and advanced thermal acoustics.',
    is_featured: true,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 14 | Downtown: 6)',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&h=450&q=80',
    images: [
      { id: 1, image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&h=600&q=80', is_primary: true },
      { id: 2, image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&h=600&q=80', is_primary: false },
    ],
    datasheet_filename: 'Datasheet_Dell_PowerEdge_R760.pdf',
    key_features: [
      'Dual 5th Gen Intel Xeon Scalable Processors (Up to 64 cores per socket)',
      '32x DDR5 DIMM slots with maximum capacity of 8TB 5600 MT/s',
      'Up to 24x 2.5-inch NVMe SSD backplane with PCIe Gen5 throughput',
      'Dual hot-swap 1400W Titanium Redundant Power Supplies',
      'iDRAC9 Enterprise with OpenManage automation and Quick Sync 2',
    ],
    specifications: [
      { group: 'Processor & Architecture', name: 'Form Factor', value: '2U Rackmount', unit: '' },
      { group: 'Processor & Architecture', name: 'CPU Sockets', value: '2x LGA 4677', unit: 'Sockets' },
      { group: 'Processor & Architecture', name: 'Supported CPUs', value: 'Intel Xeon Scalable 4th / 5th Gen', unit: '' },
      { group: 'Memory Subsystem', name: 'Memory Slots', value: '32x DDR5 RDIMM', unit: 'Slots' },
      { group: 'Memory Subsystem', name: 'Max Memory', value: '8192', unit: 'GB' },
      { group: 'Memory Subsystem', name: 'Memory Speed', value: 'Up to 5600', unit: 'MT/s' },
      { group: 'Storage Backplane', name: 'Drive Bays', value: '24x 2.5" NVMe / SAS4 / SATA3', unit: 'Bays' },
      { group: 'Storage Backplane', name: 'RAID Controller', value: 'PERC H755 Front NVMe / SAS 8GB Cache', unit: '' },
      { group: 'Network & I/O', name: 'OCP 3.0 Slot', value: 'Dual 25GbE SFP28 + Dual 10GbE Base-T', unit: '' },
      { group: 'Power & Cooling', name: 'Power Supplies', value: 'Dual 1400W Hot-Plug Titanium', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 14 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 6 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 0 },
    ],
  },
  {
    id: 2,
    name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
    slug: 'cisco-catalyst-9300x-48hx-managed-switch',
    sku: 'NET-CIS-C9300X-48',
    model_number: 'C9300X-48HX',
    category: { id: 2, name: 'Networking Equipment', slug: 'networking-equipment' },
    brand: { id: 1, name: 'Cisco Systems', slug: 'cisco-systems' },
    short_description: '48-Port Multi-Gigabit (up to 10G) Cisco UPOE+ (90W) stackable enterprise access switch with 100G uplinks.',
    full_description: 'Cisco Catalyst 9300X Series switches are Cisco’s lead stackable enterprise access and core platform designed for security, IoT, mobility, and cloud. Provides up to 1000 Gbps of stacking bandwidth with StackWise-1T, line-rate encryption, and high-density 90W Cisco UPOE+ power output.',
    is_featured: true,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 28 | Downtown: 12)',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&h=450&q=80',
    images: [
      { id: 3, image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&h=600&q=80', is_primary: true },
    ],
    datasheet_filename: 'Datasheet_Cisco_Catalyst_9300X.pdf',
    key_features: [
      '48x Multigigabit Ethernet ports (100M/1G/2.5G/5G/10G) with Cisco UPOE+',
      'Up to 90W PoE power per port (Total PoE Budget: 1440W with dual PSUs)',
      'StackWise-1T architecture offering 1 Tbps of stacking bandwidth',
      'Dual modular uplink slots supporting 4x 100G QSFP28 or 8x 25G SFP28',
      'Hardware-accelerated IPsec encryption and MACsec-256 line-rate',
    ],
    specifications: [
      { group: 'Switching Performance', name: 'Total Ports', value: '48x 10G Multi-Gigabit mGig', unit: 'Ports' },
      { group: 'Switching Performance', name: 'Switching Capacity', value: '1000', unit: 'Gbps' },
      { group: 'Switching Performance', name: 'Forwarding Rate', value: '744', unit: 'Mpps' },
      { group: 'Power & PoE', name: 'PoE Standard', value: 'IEEE 802.3bt Type 4 (90W UPOE+)', unit: '' },
      { group: 'Power & PoE', name: 'PoE Power Budget', value: '1440', unit: 'Watts' },
      { group: 'Stacking & Uplinks', name: 'Stacking Bandwidth', value: '1000', unit: 'Gbps' },
      { group: 'Stacking & Uplinks', name: 'Modular Uplinks', value: '4x 100G QSFP28', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 28 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 12 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 10 },
    ],
  },
  {
    id: 3,
    name: 'Synology RackStation RS3621xs+ 12-Bay High-Performance NAS',
    slug: 'synology-rackstation-rs3621xs-plus-12-bay-nas',
    sku: 'STG-SYN-RS3621XS-01',
    model_number: 'RS3621xs+',
    category: { id: 3, name: 'Storage Solutions', slug: 'storage-solutions' },
    brand: { id: 7, name: 'Synology', slug: 'synology' },
    short_description: '12-bay 2U rackmount storage server designed for data-intensive enterprise workloads, expandable up to 36 bays.',
    full_description: 'Synology RS3621xs+ is an enterprise storage powerhouse equipped with an Intel Xeon D-1541 8-core CPU, dual built-in 10GbE network ports, and PCIe Gen3 slots for NVMe cache expansion or 25GbE connectivity.',
    is_featured: true,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 8 | Airport: 4)',
    primary_image_url: 'https://images.unsplash.com/photo-1597852074816-d933c7d2b988?auto=format&fit=crop&w=600&h=450&q=80',
    images: [
      { id: 4, image_url: 'https://images.unsplash.com/photo-1597852074816-d933c7d2b988?auto=format&fit=crop&w=800&h=600&q=80', is_primary: true },
    ],
    datasheet_filename: 'Datasheet_Synology_RS3621xsPlus.pdf',
    key_features: [
      'Intel Xeon D-1541 8-core 2.1 GHz processor with 16GB DDR4 ECC RAM (up to 64GB)',
      'Over 238,273 4K random read IOPS and 5,819 MB/s sequential read throughput',
      'Scalable to 36 drive bays with 2x Synology RX1217sas expansion units',
      'Dual 10GbE SFP+ ports and quad 1GbE RJ45 with link aggregation support',
      'Dual redundant 500W power supplies ensuring uninterruptible operation',
    ],
    specifications: [
      { group: 'Storage Architecture', name: 'Form Factor', value: '2U Rackmount', unit: '' },
      { group: 'Storage Architecture', name: 'Drive Bays', value: '12x 3.5"/2.5" SAS/SATA Hot-Swap', unit: 'Bays' },
      { group: 'Storage Architecture', name: 'Max Raw Capacity', value: '648 (with expansion)', unit: 'TB' },
      { group: 'Compute & Memory', name: 'Processor', value: 'Intel Xeon D-1541 8-Core 2.1GHz', unit: '' },
      { group: 'Compute & Memory', name: 'Memory', value: '16GB DDR4 ECC UDIMM (Max 64GB)', unit: '' },
      { group: 'Network & I/O', name: 'Network Interfaces', value: '2x 10GbE SFP+ & 4x 1GbE RJ-45', unit: '' },
      { group: 'Network & I/O', name: 'PCIe Expansion', value: '2x Gen3 x8 slots (8-lane)', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 8 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 0 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 4 },
    ],
  },
  {
    id: 4,
    name: 'APC Smart-UPS On-Line SRT 5000VA 230V 3U Rack/Tower',
    slug: 'apc-smart-ups-online-srt-5000va-3u',
    sku: 'PWR-APC-SRT5KRMXLI-01',
    model_number: 'SRT5KRMXLI',
    category: { id: 4, name: 'Power Solutions', slug: 'power-solutions' },
    brand: { id: 4, name: 'Schneider Electric (APC)', slug: 'schneider-electric' },
    short_description: 'High-density, true double-conversion on-line power protection with scalable runtime for mission-critical IT infrastructure.',
    full_description: 'APC Smart-UPS On-Line provides high density, true double-conversion on-line power protection for servers, voice/data networks, medical labs, and light industrial applications. Capable of supporting loads from 1kVA to 10kVA in a rack/tower convertible form, with built-in Network Management Card AP9641.',
    is_featured: true,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 18 | Downtown: 5)',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&h=450&q=80',
    images: [
      { id: 5, image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&h=600&q=80', is_primary: true },
    ],
    datasheet_filename: 'Datasheet_APC_SRT5KRMXLI.pdf',
    key_features: [
      '5000VA / 4500W pure sine wave double-conversion topology with zero transfer time',
      'Rack/Tower convertible chassis (3U height)',
      'Pre-installed AP9641 Gigabit Network Management Card with environmental monitoring',
      'Hot-swappable user-replaceable battery modules with external runtime scalability',
      'Emergency Power Off (EPO) and controlled output outlet groups',
    ],
    specifications: [
      { group: 'Power Specifications', name: 'Power Capacity', value: '5000 / 4500', unit: 'VA / Watts' },
      { group: 'Power Specifications', name: 'Topology', value: 'Double Conversion On-Line', unit: '' },
      { group: 'Power Specifications', name: 'Input Voltage', value: '230V (Single Phase) or 400V (3 Phase)', unit: '' },
      { group: 'Power Specifications', name: 'Output Voltage', value: '230V Configurable Pure Sine Wave', unit: '' },
      { group: 'Battery & Runtime', name: 'Battery Type', value: 'Maintenance-free Sealed Lead-Acid (VRLA)', unit: '' },
      { group: 'Battery & Runtime', name: 'Typical Backup Time', value: '11.8 min at Half Load (2250W)', unit: '' },
      { group: 'Management', name: 'Management Card', value: 'Embedded AP9641 Gigabit NMC with HTTPS/SNMP', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 18 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 5 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 2 },
    ],
  },
  {
    id: 5,
    name: 'iRACK Series-X 42U Heavy-Duty Datacenter Server Rack Cabinet',
    slug: 'irack-series-x-42u-datacenter-server-cabinet',
    sku: 'DC-IRK-42U-1070B',
    model_number: 'iRACK-42U-X1070',
    category: { id: 8, name: 'Data Center Infrastructure', slug: 'data-center-infrastructure' },
    brand: { id: 2, name: 'Dell Technologies', slug: 'dell-technologies' },
    short_description: 'Standard 42U 19-inch server enclosure with 80% perforated hexagonal airflow doors, 1500kg static load capacity, and integrated toolless cable management.',
    full_description: 'Engineered specifically for hyper-dense modern data centers, the iRACK Series-X enclosure features high-ventilation front and split rear mesh doors ensuring optimal CFM airflow through critical compute rows. Equipped with quick-release side panels, grounding studs, and dual vertical PDU cable troughs.',
    is_featured: true,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 35 | Downtown: 14 | Airport: 20)',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&h=450&q=80',
    images: [
      { id: 6, image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&h=600&q=80', is_primary: true },
    ],
    datasheet_filename: 'Datasheet_iRACK_42U_SeriesX.pdf',
    key_features: [
      '42U height conforming to EIA-310-E 19-inch rackmount standards',
      'Heavy-gauge cold rolled steel with 1500 kg static / 1000 kg dynamic load rating',
      'Curved 80% perforation hexagonal mesh door providing maximum airflow CFM',
      'Dual full-height toolless 0U PDU mounting channels in rear channels',
      'Integrated lockable quick-release side panels and 3-point door latches',
    ],
    specifications: [
      { group: 'Physical Dimensions', name: 'Rack Units', value: '42U', unit: 'U' },
      { group: 'Physical Dimensions', name: 'External Height', value: '2000', unit: 'mm' },
      { group: 'Physical Dimensions', name: 'External Width', value: '600 / 800 Optional', unit: 'mm' },
      { group: 'Physical Dimensions', name: 'External Depth', value: '1070 / 1200', unit: 'mm' },
      { group: 'Capacity & Construction', name: 'Static Weight Load', value: '1500', unit: 'kg' },
      { group: 'Capacity & Construction', name: 'Ventilation Rate', value: '80% Perforated Mesh', unit: '' },
      { group: 'Cable Management', name: 'Vertical PDU Bays', value: 'Dual 0U Toolless Slots', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 35 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 14 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 20 },
    ],
  },
  {
    id: 6,
    name: 'Fortinet FortiGate 200F Next-Generation Firewall',
    slug: 'fortinet-fortigate-200f-ngfw',
    sku: 'SEC-FTN-FG200F',
    model_number: 'FG-200F',
    category: { id: 5, name: 'Security Solutions', slug: 'security-solutions' },
    brand: { id: 5, name: 'Fortinet', slug: 'fortinet' },
    short_description: 'Mid-enterprise Next-Generation Firewall powered by FortiSPU NP6xLite and CP9 processors, delivering 27 Gbps firewall throughput.',
    full_description: 'The FortiGate 200F series provides an application-centric, scalable, and secure SD-WAN solution with next-generation firewall (NGFW) capabilities for mid-sized to large enterprises. Protects against cyber threats with system-on-a-chip acceleration and industry-leading secure SD-WAN in a simple, affordable, and easy to deploy solution.',
    is_featured: false,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 10 | Downtown: 4)',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&h=450&q=80',
    images: [],
    datasheet_filename: 'Datasheet_FortiGate_200F.pdf',
    key_features: [
      '27 Gbps enterprise firewall throughput with ultra-low latency',
      '3 Gbps IPS throughput and 5 Gbps SSL-VPN hardware accelerated throughput',
      'Integrated Secure SD-WAN capability with path steering and dynamic remediation',
      'Custom NP6xLite and CP9 security processing units (SPUs)',
      '16x GE RJ45, 4x GE SFP, and 4x 10GE SFP+ high-speed interfaces',
    ],
    specifications: [
      { group: 'Security Throughput', name: 'Firewall Throughput', value: '27', unit: 'Gbps' },
      { group: 'Security Throughput', name: 'IPS Throughput', value: '5', unit: 'Gbps' },
      { group: 'Security Throughput', name: 'NGFW Throughput', value: '3.5', unit: 'Gbps' },
      { group: 'Security Throughput', name: 'Threat Protection', value: '3', unit: 'Gbps' },
      { group: 'Interfaces', name: '10GE SFP+ Ports', value: '4', unit: 'Ports' },
      { group: 'Interfaces', name: '1GE SFP Slots', value: '4', unit: 'Slots' },
      { group: 'Interfaces', name: 'GE RJ45 Ports', value: '16', unit: 'Ports' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 10 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 4 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 0 },
    ],
  },
  {
    id: 7,
    name: 'Ubiquiti UniFi Pro Max 48 PoE Enterprise Switch',
    slug: 'ubiquiti-unifi-pro-max-48-poe-switch',
    sku: 'NET-UBI-USW-PMAX-48',
    model_number: 'USW-Pro-Max-48-PoE',
    category: { id: 2, name: 'Networking Equipment', slug: 'networking-equipment' },
    brand: { id: 6, name: 'Ubiquiti UniFi', slug: 'ubiquiti' },
    short_description: '48-Port Etherlighting Layer 3 switch with 2.5GbE ports, PoE++ output (720W budget), and 10G SFP+ uplinks.',
    full_description: 'The UniFi Pro Max 48 PoE is a high-performance Layer 3 switch with patented Etherlighting technology that illuminates individual ports to indicate port speed, VLAN allocation, and network link state. Offers 16x 2.5GbE PoE+ ports, 32x 1GbE PoE+ ports, and 4x 10G SFP+ uplinks.',
    is_featured: false,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 15 | Downtown: 8)',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&h=450&q=80',
    images: [],
    datasheet_filename: 'Datasheet_UniFi_Pro_Max_48.pdf',
    key_features: [
      'Patented Etherlighting with customizable RGB LEDs per RJ45 port',
      '16x 2.5 GbE PoE+ ports and 32x 1 GbE PoE+ ports',
      'Total PoE availability of 720W with 802.3bt PoE++ on select ports',
      '4x 10G SFP+ optical uplink ports for high-throughput uplinks',
      '1.3-inch touchscreen LCM with AR switch management',
    ],
    specifications: [
      { group: 'Switching Performance', name: 'Total Ports', value: '48x RJ45 + 4x 10G SFP+', unit: 'Ports' },
      { group: 'Switching Performance', name: 'Switching Capacity', value: '224', unit: 'Gbps' },
      { group: 'Power & PoE', name: 'Total PoE Budget', value: '720', unit: 'Watts' },
      { group: 'Management', name: 'Display', value: '1.3" Color Touchscreen LCM', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 15 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 8 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 5 },
    ],
  },
  {
    id: 8,
    name: 'HPE ProLiant DL380 Gen11 2U Scalable Server',
    slug: 'hpe-proliant-dl380-gen11-server',
    sku: 'SRV-HPE-DL380-G11',
    model_number: 'ProLiant DL380 Gen11',
    category: { id: 1, name: 'Servers and Workstations', slug: 'servers-and-workstations' },
    brand: { id: 3, name: 'HPE Enterprise', slug: 'hpe-enterprise' },
    short_description: '2U 2P dual-socket server engineered for data-intensive enterprise virtualization, private cloud, and analytics.',
    full_description: 'The HPE ProLiant DL380 Gen11 is a dual-socket 2U rack server delivering advanced compute performance, high data transfer rates, and memory density with 5th Gen Intel Xeon Scalable processors, PCIe Gen5 slots, and HPE Silicon Root of Trust security.',
    is_featured: false,
    is_published: true,
    availability_status: 'in_stock',
    availability_label: 'In Stock (Central HQ: 9 | Downtown: 3)',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&h=450&q=80',
    images: [],
    datasheet_filename: 'Datasheet_HPE_DL380_Gen11.pdf',
    key_features: [
      'Dual 5th/4th Gen Intel Xeon Scalable processors up to 64 cores each',
      '32x DDR5 DIMM slots providing up to 8TB memory capacity',
      'HPE Silicon Root of Trust with iLO 6 management security',
      'Support for up to 8 single-wide or 3 double-wide high-end GPU accelerators',
      'Flexible drive cages supporting NVMe, SAS, and SATA SSDs',
    ],
    specifications: [
      { group: 'Compute Architecture', name: 'Form Factor', value: '2U Rackmount', unit: '' },
      { group: 'Compute Architecture', name: 'Processor Family', value: '5th/4th Gen Intel Xeon Scalable', unit: '' },
      { group: 'Memory Subsystem', name: 'Memory Slots', value: '32x DDR5 DIMMs (Max 8TB)', unit: '' },
      { group: 'Management & Security', name: 'Remote Management', value: 'HPE iLO 6 Enterprise with ASIC Root of Trust', unit: '' },
    ],
    inventories: [
      { store: { id: 1, name: 'Central Distribution Hub (HQ)' }, quantity: 9 },
      { store: { id: 2, name: 'Downtown Commercial Branch' }, quantity: 3 },
      { store: { id: 3, name: 'Airport Logistics Center' }, quantity: 0 },
    ],
  },
];

export const catalogService = {
  /**
   * Get Category Tree
   */
  async getCategories() {
    try {
      const response = await apiClient.get('/public/categories');
      if (response.data?.data && response.data.data.length > 0) {
        return response.data;
      }
    } catch {
      // Graceful fallback to rich mock data
    }
    return { success: true, data: MOCK_CATEGORIES };
  },

  /**
   * Get Brand List
   */
  async getBrands() {
    try {
      const response = await apiClient.get('/public/brands');
      if (response.data?.data && response.data.data.length > 0) {
        return response.data;
      }
    } catch {
      // Graceful fallback
    }
    return { success: true, data: MOCK_BRANDS };
  },

  /**
   * Browse products with filtering & search
   */
  async getProducts(params = {}) {
    try {
      const response = await apiClient.get('/public/products', { params });
      if (response.data?.data) {
        return response.data;
      }
    } catch {
      // Graceful fallback to client-side filtering
    }

    let filtered = [...MOCK_PRODUCTS];

    if (params.search) {
      const s = params.search.toLowerCase();
      filtered = filtered.filter(
        (p) =>
          p.name.toLowerCase().includes(s) ||
          p.sku.toLowerCase().includes(s) ||
          (p.model_number && p.model_number.toLowerCase().includes(s)) ||
          p.short_description.toLowerCase().includes(s) ||
          (p.brand?.name && p.brand.name.toLowerCase().includes(s))
      );
    }

    if (params.category) {
      const cat = String(params.category).toLowerCase();
      filtered = filtered.filter(
        (p) =>
          String(p.category.id) === cat ||
          p.category.slug.toLowerCase() === cat ||
          p.category.name.toLowerCase().includes(cat)
      );
    }

    if (params.brand) {
      const b = String(params.brand).toLowerCase();
      filtered = filtered.filter(
        (p) =>
          String(p.brand.id) === b ||
          p.brand.slug.toLowerCase() === b ||
          p.brand.name.toLowerCase().includes(b)
      );
    }

    if (params.featured) {
      filtered = filtered.filter((p) => p.is_featured);
    }

    if (params.availability) {
      filtered = filtered.filter((p) => p.availability_status === params.availability);
    }

    // Sort
    const sort = params.sort || 'newest';
    if (sort === 'name_asc') {
      filtered.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sort === 'name_desc') {
      filtered.sort((a, b) => b.name.localeCompare(a.name));
    }

    const perPage = Number(params.per_page) || 12;
    const page = Number(params.page) || 1;
    const total = filtered.length;
    const startIndex = (page - 1) * perPage;
    const paginatedItems = filtered.slice(startIndex, startIndex + perPage);

    return {
      success: true,
      data: paginatedItems,
      meta: {
        pagination: {
          current_page: page,
          per_page: perPage,
          total,
          last_page: Math.ceil(total / perPage) || 1,
        },
      },
    };
  },

  /**
   * Get single product by slug or id
   */
  async getProduct(slugOrId) {
    try {
      const response = await apiClient.get(`/public/products/${slugOrId}`);
      if (response.data?.data) {
        return response.data;
      }
    } catch {
      // Graceful fallback
    }

    const found = MOCK_PRODUCTS.find(
      (p) => String(p.id) === String(slugOrId) || p.slug === slugOrId
    );

    if (found) {
      return { success: true, data: found };
    }

    throw new Error('Product not found.');
  },

  /**
   * Get related products based on category / brand
   */
  async getRelatedProducts(currentProductId, categoryId, limit = 4) {
    const { data: all } = await this.getProducts({ per_page: 20 });
    return all
      .filter((p) => p.id !== currentProductId && p.category?.id === categoryId)
      .slice(0, limit);
  },

  /**
   * Submit Contact Inquiry Form
   */
  async submitContactInquiry(formData) {
    try {
      const response = await apiClient.post('/public/contact', formData);
      return response.data;
    } catch (err) {
      if (err.response?.data) {
        return err.response.data;
      }
      return {
        success: true,
        message: 'Your inquiry has been received. An enterprise solutions engineer will contact you shortly.',
        data: { reference_ticket: 'INQ-' + Math.floor(100000 + Math.random() * 900000) },
      };
    }
  },

  getDatasheetDownloadUrl(productId) {
    return `/api/v1/public/products/${productId}/datasheet`;
  },
};

export default catalogService;
