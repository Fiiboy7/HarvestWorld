// Contoh data untuk dimasukkan ke tabel plants di Supabase
const plantsData = [
  {
    name: "Tomat",
    scientific_name: "Solanum lycopersicum",
    category: "Sayuran",
    climate: "Sedang",
    difficulty: "Mudah",
    description:
      "Tanaman yang banyak ditanam dalam keluarga Solanaceae, tomat adalah bahan utama dalam banyak hidangan di seluruh dunia.",
    long_description:
      "Tomat adalah buah yang dapat dimakan dari tanaman Solanum lycopersicum, umumnya dikenal sebagai tanaman tomat. Spesies ini berasal dari Amerika Selatan dan Amerika Tengah bagian barat. Tomat kaya akan vitamin C dan likopen, yang memiliki banyak manfaat kesehatan.",
    image_url:
      "https://images.unsplash.com/photo-1592841200221-a6898f307baa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
    growing_conditions: {
      soil: "Tanah yang drainase baik, sedikit asam (pH 6.0-6.8)",
      sunlight: "Sinar matahari penuh (6-8 jam sehari)",
      water: "Penyiraman teratur, menjaga tanah tetap lembab",
      temperature: "18-29°C",
      spacing: "60-90 cm antar tanaman",
    },
    growing_calendar: {
      planting: "Akhir musim semi setelah embun beku terakhir",
      germination: "5-10 hari",
      growth: "60-100 hari hingga matang",
      harvest: "Musim panas hingga awal musim gugur",
    },
    common_problems: ["Busuk ujung bunga", "Hawar daun awal", "Hawar daun akhir", "Kutu daun", "Ulat tanduk tomat"],
    tips: [
      "Pasang tiang atau sangkar untuk dukungan",
      "Pangkas tunas untuk varietas indeterminate",
      "Gunakan mulsa untuk mempertahankan kelembaban dan mencegah penyakit",
      "Rotasi tanaman setiap tahun untuk mencegah penumpukan penyakit",
      "Panen saat buah sudah keras dan berwarna penuh",
    ],
    related_plants: [
      { id: 6, name: "Cabai", category: "Sayuran" },
      { id: 7, name: "Terong", category: "Sayuran" },
      { id: 8, name: "Kentang", category: "Sayuran" },
    ],
  },
  {
    name: "Padi",
    scientific_name: "Oryza sativa",
    category: "Biji-bijian",
    climate: "Tropis",
    difficulty: "Sedang",
    description:
      "Makanan pokok bagi lebih dari setengah populasi dunia, beras adalah biji-bijian yang paling banyak dikonsumsi.",
    long_description:
      "Padi adalah tanaman pangan penting yang menghasilkan beras, makanan pokok bagi lebih dari setengah populasi dunia, terutama di Asia. Tanaman ini termasuk dalam keluarga rumput-rumputan dan tumbuh di daerah dengan curah hujan tinggi atau memerlukan irigasi yang baik.",
    image_url:
      "https://images.unsplash.com/photo-1536054695850-b45f0fb89a76?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
    growing_conditions: {
      soil: "Tanah liat atau lempung dengan drainase yang baik",
      sunlight: "Sinar matahari penuh",
      water: "Membutuhkan banyak air, terutama saat pembentukan malai",
      temperature: "20-35°C",
      spacing: "20-25 cm antar tanaman",
    },
    growing_calendar: {
      planting: "Awal musim hujan",
      germination: "5-10 hari",
      growth: "3-6 bulan tergantung varietas",
      harvest: "Saat bulir padi sudah menguning dan berisi penuh",
    },
    common_problems: ["Wereng coklat", "Penggerek batang", "Penyakit blast", "Penyakit hawar daun bakteri", "Tikus"],
    tips: [
      "Gunakan varietas yang sesuai dengan kondisi lokal",
      "Lakukan pengairan yang tepat sesuai fase pertumbuhan",
      "Terapkan pengendalian hama terpadu",
      "Gunakan pupuk berimbang",
      "Panen pada waktu yang tepat untuk kualitas beras terbaik",
    ],
    related_plants: [
      { id: 2, name: "Gandum", category: "Biji-bijian" },
      { id: 6, name: "Jagung", category: "Biji-bijian" },
    ],
  },
  {
    name: "Cabai",
    scientific_name: "Capsicum annuum",
    category: "Sayuran",
    climate: "Tropis",
    difficulty: "Sedang",
    description:
      "Tanaman yang menghasilkan buah pedas yang digunakan sebagai bumbu di banyak masakan di seluruh dunia.",
    long_description:
      "Cabai adalah tanaman dari genus Capsicum yang menghasilkan buah dengan rasa pedas karena kandungan capsaicin. Tanaman ini berasal dari Amerika dan sekarang dibudidayakan di seluruh dunia. Cabai kaya akan vitamin C dan memiliki banyak varietas dengan tingkat kepedasan yang berbeda.",
    image_url:
      "https://images.unsplash.com/photo-1588252303782-cb80119abd6d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
    growing_conditions: {
      soil: "Tanah yang kaya bahan organik dengan drainase baik",
      sunlight: "Sinar matahari penuh (6-8 jam sehari)",
      water: "Penyiraman teratur, hindari kelebihan air",
      temperature: "20-32°C",
      spacing: "45-60 cm antar tanaman",
    },
    growing_calendar: {
      planting: "Awal musim kemarau",
      germination: "7-14 hari",
      growth: "70-90 hari hingga panen pertama",
      harvest: "Panen berkelanjutan selama beberapa bulan",
    },
    common_problems: ["Kutu daun", "Thrips", "Penyakit busuk buah", "Penyakit layu fusarium", "Virus keriting daun"],
    tips: [
      "Gunakan ajir untuk menopang tanaman",
      "Pangkas daun bagian bawah untuk sirkulasi udara",
      "Hindari menyiram daun untuk mencegah penyakit jamur",
      "Panen secara teratur untuk merangsang produksi buah",
      "Gunakan mulsa untuk menjaga kelembaban tanah",
    ],
    related_plants: [
      { id: 1, name: "Tomat", category: "Sayuran" },
      { id: 7, name: "Terong", category: "Sayuran" },
      { id: 8, name: "Paprika", category: "Sayuran" },
    ],
  },
]

// Kode untuk memasukkan data ke Supabase
// Ini hanya contoh, Anda perlu menjalankannya di lingkungan Node.js

// Assuming you are using Supabase client library
const { createClient } = require("@supabase/supabase-js")

// Replace with your Supabase URL and API key
const supabaseUrl = "YOUR_SUPABASE_URL"
const supabaseKey = "YOUR_SUPABASE_ANON_KEY"

// Create a Supabase client
const supabase = createClient(supabaseUrl, supabaseKey)

async function seedPlants() {
  const { data, error } = await supabase.from("plants").insert(plantsData).select()

  if (error) {
    console.error("Error seeding plants:", error)
    return
  }

  console.log("Plants seeded successfully:", data)
}

// seedPlants();
