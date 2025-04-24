import Image from "next/image"
import Link from "next/link"
import { MapPin, Mail, Phone, ArrowRight } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"

export default function AboutPage() {
  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      {/* Hero Section */}
      <section className="relative py-20 bg-green-700">
        <div className="absolute inset-0 z-0">
          <Image
            src="/placeholder.svg?height=400&width=1920&text=Tentang+HarvestWorld"
            alt="Tentang HarvestWorld"
            fill
            style={{ objectFit: "cover" }}
          />
          <div className="absolute inset-0 bg-green-900/70"></div>
        </div>

        <div className="container mx-auto px-4 relative z-10 text-white">
          <h1 className="text-4xl md:text-5xl font-bold mb-4 text-center">Tentang HarvestWorld</h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-center">
            Misi kami adalah menyediakan sumber daya pendidikan berkualitas untuk pengetahuan tanaman pertanian dan
            praktik pertanian berkelanjutan
          </p>
        </div>
      </section>

      {/* Our Story */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-3xl font-bold mb-8 text-green-800 text-center">Cerita Kami</h2>
            <div className="prose prose-lg max-w-none text-gray-700">
              <p>
                Sistem informasi edukasi tanaman pertanian ini dikembangkan pada tahun 2025 oleh sekelompok pemuda yang
                peduli terhadap masa depan pertanian Indonesia. Berangkat dari visi untuk menjadikan edukasi pertanian
                lebih mudah diakses, sistem ini bertujuan membantu petani, pelajar, dan masyarakat umum dalam memahami
                praktik budidaya yang efektif dan berkelanjutan.
              </p>
              <p>
                Dengan menghadirkan konten yang informatif, praktis, dan terus diperbarui, platform ini diharapkan
                menjadi sarana belajar yang inklusif serta mendorong terciptanya komunitas pertanian yang saling berbagi
                pengetahuan dan pengalaman.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Our Team */}
      <section className="py-16 bg-green-50">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-12 text-green-800 text-center">Tim Kami</h2>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              {
                name: "Dr. Adi Wijaya",
                role: "Pendiri & CEO",
                bio: "Ahli pertanian dengan pengalaman lebih dari 15 tahun dalam penelitian tanaman pangan.",
                image: "adi-wijaya",
              },
              {
                name: "Siti Nurhaliza",
                role: "Kepala Peneliti",
                bio: "Spesialis dalam pertanian berkelanjutan dan teknik budidaya organik.",
                image: "siti-nurhaliza",
              },
              {
                name: "Budi Santoso",
                role: "Pengembang Web",
                bio: "Pengembang full-stack dengan hasrat untuk teknologi pertanian.",
                image: "budi-santoso",
              },
              {
                name: "Dewi Putri",
                role: "Manajer Konten",
                bio: "Editor berpengalaman dengan latar belakang dalam jurnalisme ilmiah.",
                image: "dewi-putri",
              },
            ].map((member, index) => (
              <div key={index} className="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all">
                <div className="h-64 relative">
                  <Image
                    src={`/placeholder.svg?height=300&width=300&text=${member.image.replace("-", " ")}`}
                    alt={member.name}
                    fill
                    style={{ objectFit: "cover" }}
                  />
                </div>
                <div className="p-6">
                  <h3 className="text-xl font-bold mb-1 text-gray-800">{member.name}</h3>
                  <p className="text-green-600 font-medium mb-3">{member.role}</p>
                  <p className="text-gray-600 mb-4">{member.bio}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Our Mission */}
      <section className="py-16 bg-green-700 text-white">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto text-center">
            <h2 className="text-3xl font-bold mb-8">Misi Kami</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="bg-white/10 p-6 rounded-lg backdrop-blur-sm">
                <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                    />
                  </svg>
                </div>
                <h3 className="text-xl font-bold mb-3">Pendidikan</h3>
                <p>
                  Menyediakan sumber daya pendidikan berkualitas tinggi tentang praktik pertanian berkelanjutan dan
                  pengetahuan tanaman.
                </p>
              </div>
              <div className="bg-white/10 p-6 rounded-lg backdrop-blur-sm">
                <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                    />
                  </svg>
                </div>
                <h3 className="text-xl font-bold mb-3">Komunitas</h3>
                <p>Membangun komunitas petani yang saling mendukung untuk berbagi pengetahuan dan pengalaman.</p>
              </div>
              <div className="bg-white/10 p-6 rounded-lg backdrop-blur-sm">
                <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020 5.5v-1.5"
                    />
                  </svg>
                </div>
                <h3 className="text-xl font-bold mb-3">Keberlanjutan</h3>
                <p>
                  Mempromosikan praktik pertanian yang ramah lingkungan dan berkelanjutan untuk masa depan yang lebih
                  baik.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Us */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-12 text-green-800 text-center">Hubungi Kami</h2>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div>
              <div className="bg-green-50 p-6 rounded-lg">
                <h3 className="text-xl font-bold mb-4 text-green-800">Informasi Kontak</h3>
                <ul className="space-y-4">
                  <li className="flex items-start">
                    <MapPin className="text-green-600 mr-3 flex-shrink-0 mt-1" size={20} />
                    <span>Jl. Pertanian No. 123, Jakarta Selatan, Indonesia 12345</span>
                  </li>
                  <li className="flex items-start">
                    <Mail className="text-green-600 mr-3 flex-shrink-0 mt-1" size={20} />
                    <span>info@harvestworld.com</span>
                  </li>
                  <li className="flex items-start">
                    <Phone className="text-green-600 mr-3 flex-shrink-0 mt-1" size={20} />
                    <span>+62 123 4567 890</span>
                  </li>
                </ul>

                <div className="mt-6">
                  <h4 className="font-semibold mb-2">Jam Operasional:</h4>
                  <p>Senin - Jumat: 09:00 - 17:00</p>
                  <p>Sabtu: 09:00 - 13:00</p>
                  <p>Minggu: Tutup</p>
                </div>
              </div>
            </div>

            <div>
              <form className="bg-green-50 p-6 rounded-lg">
                <h3 className="text-xl font-bold mb-4 text-green-800">Kirim Pesan</h3>
                <div className="space-y-4">
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                      Nama
                    </label>
                    <input
                      type="text"
                      id="name"
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                  </div>
                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                      Email
                    </label>
                    <input
                      type="email"
                      id="email"
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                  </div>
                  <div>
                    <label htmlFor="subject" className="block text-sm font-medium text-gray-700 mb-1">
                      Subjek
                    </label>
                    <input
                      type="text"
                      id="subject"
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                  </div>
                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                      Pesan
                    </label>
                    <textarea
                      id="message"
                      rows={4}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    ></textarea>
                  </div>
                  <button
                    type="submit"
                    className="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-medium transition-colors"
                  >
                    Kirim Pesan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 bg-green-700 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold mb-4">Bergabunglah dengan Komunitas Kami</h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto">
            Jadilah bagian dari komunitas petani yang berkembang dan akses sumber daya pendidikan kami
          </p>
          <Link
            href="/signup"
            className="inline-flex items-center bg-white text-green-700 hover:bg-green-100 py-3 px-8 rounded-full font-medium transition-all duration-300 transform hover:scale-105"
          >
            Daftar Sekarang
            <ArrowRight className="ml-2" size={18} />
          </Link>
        </div>
      </section>

      <Footer />
    </div>
  )
}
