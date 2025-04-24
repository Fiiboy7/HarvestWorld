import Link from "next/link"
import { ArrowRight } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import RecentArticles from "@/components/recent-articles"
import ImageWithFallback from "@/components/image-with-fallback"

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-green-50 to-green-100">
      <Navbar />

      {/* Hero Section with Parallax Effect */}
      <section className="relative h-[90vh] overflow-hidden">
        <div className="absolute inset-0 z-0">
          <ImageWithFallback
            src="/placeholder.svg?height=1080&width=1920&text=Pertanian+Indah"
            alt="Pemandangan pertanian"
            fill
            style={{ objectFit: "cover" }}
            priority
            className="transform scale-110 origin-center"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-green-900/70 to-green-800/50"></div>
        </div>

        <div className="container mx-auto px-4 h-full flex items-center relative z-10">
          <div className="max-w-3xl text-white">
            <h1 className="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
              Temukan <span className="text-green-300">Dunia Tanaman</span>
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-green-50 animate-fade-in-delay">
              Jelajahi database komprehensif kami tentang tanaman pertanian, panduan budidaya, dan tips ahli untuk
              membantu Anda mengembangkan kebun yang subur.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 animate-fade-in-delay-2">
              <Link
                href="/plants"
                className="bg-green-500 hover:bg-green-600 text-white py-3 px-8 rounded-full font-medium transition-all duration-300 transform hover:scale-105 flex items-center justify-center group"
              >
                Jelajahi Tanaman
                <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={18} />
              </Link>
              <Link
                href="/articles"
                className="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white py-3 px-8 rounded-full font-medium transition-all duration-300"
              >
                Panduan Budidaya
              </Link>
            </div>
          </div>
        </div>

        {/* Animated scroll indicator */}
        <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
          <div className="w-8 h-12 rounded-full border-2 border-white/50 flex items-center justify-center">
            <div className="w-1.5 h-3 bg-white/50 rounded-full animate-scroll-indicator"></div>
          </div>
        </div>
      </section>

      {/* Featured Categories with Hover Effects */}
      <section className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-800 mb-4">Jelajahi Kategori Tanaman</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Temukan tanaman yang diorganisir berdasarkan jenis untuk menemukan apa yang Anda cari
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              {
                name: "Sayuran",
                icon: "🥕",
                description: "Tanaman pangan penting untuk kebun Anda",
                color: "from-green-500 to-green-600",
                slug: "sayuran",
              },
              {
                name: "Buah-buahan",
                icon: "🍎",
                description: "Favorit kebun yang manis dan bergizi",
                color: "from-red-500 to-red-600",
                slug: "buah-buahan",
              },
              {
                name: "Biji-bijian",
                icon: "🌾",
                description: "Tanaman pokok yang memberi makan dunia",
                color: "from-yellow-500 to-yellow-600",
                slug: "biji-bijian",
              },
              {
                name: "Rempah-rempah",
                icon: "🌿",
                description: "Tanaman aromatik untuk memasak dan obat",
                color: "from-emerald-500 to-emerald-600",
                slug: "rempah-rempah",
              },
            ].map((category, index) => (
              <div
                key={index}
                className="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
              >
                <div
                  className={`absolute inset-0 bg-gradient-to-br ${category.color} opacity-90 group-hover:opacity-100 transition-opacity`}
                ></div>
                <div className="relative p-8 text-white h-full flex flex-col">
                  <div className="text-5xl mb-4">{category.icon}</div>
                  <h3 className="text-2xl font-bold mb-2">{category.name}</h3>
                  <p className="mb-6">{category.description}</p>
                  <Link
                    href={`/category/${category.slug}`}
                    className="mt-auto inline-flex items-center text-white font-medium group-hover:underline"
                  >
                    Jelajahi {category.name}
                    <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={16} />
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Articles Section */}
      <section className="py-20 bg-gradient-to-b from-green-50 to-green-100">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-800 mb-4">Artikel Terbaru</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Perluas pengetahuan Anda dengan artikel ahli kami tentang pertanian berkelanjutan dan perawatan tanaman
            </p>
          </div>

          {/* Import and use the RecentArticles component */}
          <div className="mb-8">
            <RecentArticles />
          </div>

          <div className="text-center mt-12">
            <Link
              href="/articles"
              className="inline-flex items-center border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white py-3 px-8 rounded-full font-medium transition-colors"
            >
              Lihat Semua Artikel
              <ArrowRight className="ml-2" size={18} />
            </Link>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  )
}
