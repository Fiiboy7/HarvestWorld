"use client"

import { useState, useEffect } from "react"
import Image from "next/image"
import Link from "next/link"
import { Search, MessageSquare, Users, Clock, ArrowRight, Filter } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"

export default function ForumPage() {
  const [forumTopics, setForumTopics] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    async function fetchTopics() {
      try {
        setLoading(true)
        const { data, error } = await supabase
          .from("forum_topics")
          .select("*, profiles(username, full_name, avatar_url)")
          .order("created_at", { ascending: false })

        if (error) throw error

        setForumTopics(data || [])
      } catch (err) {
        console.error("Error fetching forum topics:", err)
        setError("Gagal memuat topik forum")
      } finally {
        setLoading(false)
      }
    }

    fetchTopics()
  }, [])

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      {/* Hero Section */}
      <section className="relative py-16 bg-green-700">
        <div className="absolute inset-0 z-0">
          <Image
            src="/placeholder.svg?height=400&width=1920&text=Forum+Diskusi"
            alt="Forum Diskusi"
            fill
            style={{ objectFit: "cover" }}
          />
          <div className="absolute inset-0 bg-green-900/70"></div>
        </div>

        <div className="container mx-auto px-4 relative z-10 text-white">
          <h1 className="text-4xl md:text-5xl font-bold mb-4 text-center">Forum Diskusi</h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-center">
            Bergabunglah dengan komunitas petani dan berbagi pengetahuan, pengalaman, dan solusi
          </p>

          <div className="max-w-xl mx-auto">
            <div className="relative">
              <input
                type="text"
                placeholder="Cari topik diskusi..."
                className="w-full py-3 px-4 pr-10 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500"
              />
              <Search className="absolute right-3 top-3 text-gray-500" size={20} />
            </div>
          </div>
        </div>
      </section>

      {/* Forum Content */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="flex flex-col md:flex-row gap-8">
            {/* Sidebar */}
            <div className="md:w-1/4">
              <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                <Link
                  href="/forum/new"
                  className="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
                >
                  <MessageSquare className="mr-2" size={18} />
                  Buat Topik Baru
                </Link>
              </div>

              <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 className="font-bold text-lg mb-4 text-gray-800">Kategori</h3>
                <ul className="space-y-2">
                  {[
                    "Semua Kategori",
                    "Pengendalian Hama",
                    "Varietas Tanaman",
                    "Pertanian Organik",
                    "Pertanian Perkotaan",
                    "Hidroponik",
                    "Kesehatan Tanaman",
                    "Kalender Tanam",
                    "Teknik Budidaya",
                  ].map((category, index) => (
                    <li key={index}>
                      <a
                        href="#"
                        className={`block py-1 px-2 rounded ${
                          index === 0 ? "bg-green-100 text-green-800 font-medium" : "text-gray-700 hover:bg-gray-100"
                        }`}
                      >
                        {category}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>

              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="font-bold text-lg mb-4 text-gray-800">Statistik Forum</h3>
                <ul className="space-y-3">
                  <li className="flex items-center">
                    <MessageSquare className="text-green-600 mr-2" size={18} />
                    <span>{forumTopics.length} Topik</span>
                  </li>
                  <li className="flex items-center">
                    <Users className="text-green-600 mr-2" size={18} />
                    <span>1,245 Anggota</span>
                  </li>
                  <li className="flex items-center">
                    <Clock className="text-green-600 mr-2" size={18} />
                    <span>Aktif 24 jam yang lalu</span>
                  </li>
                </ul>
              </div>
            </div>

            {/* Main Content */}
            <div className="md:w-3/4">
              <div className="bg-white rounded-lg shadow-md overflow-hidden">
                {/* Filter Bar */}
                <div className="bg-gray-50 p-4 border-b flex flex-wrap items-center justify-between gap-4">
                  <div className="flex items-center">
                    <Filter className="text-gray-500 mr-2" size={18} />
                    <span className="text-gray-700 font-medium">Filter:</span>
                    <div className="ml-4 flex flex-wrap gap-2">
                      <button className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded">
                        Terbaru
                      </button>
                      <button className="bg-gray-100 text-gray-800 hover:bg-gray-200 text-xs font-medium px-2.5 py-1 rounded">
                        Populer
                      </button>
                      <button className="bg-gray-100 text-gray-800 hover:bg-gray-200 text-xs font-medium px-2.5 py-1 rounded">
                        Belum Terjawab
                      </button>
                    </div>
                  </div>
                  <div>
                    <select className="bg-white border border-gray-300 text-gray-700 py-1 px-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                      <option>Urutkan: Terbaru</option>
                      <option>Urutkan: Terpopuler</option>
                      <option>Urutkan: Paling Banyak Dilihat</option>
                    </select>
                  </div>
                </div>

                {/* Topics List */}
                <div className="divide-y">
                  {loading ? (
                    <div className="p-12 text-center">
                      <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-green-500 mb-4"></div>
                      <p className="text-gray-500">Memuat topik forum...</p>
                    </div>
                  ) : error ? (
                    <div className="p-12 text-center">
                      <p className="text-red-500">{error}</p>
                    </div>
                  ) : forumTopics.length > 0 ? (
                    forumTopics.map((topic) => (
                      <div key={topic.id} className="p-4 hover:bg-gray-50">
                        <div className="flex items-start gap-4">
                          <div className="hidden sm:block">
                            <div className="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center text-green-700 font-bold">
                              {topic.profiles?.full_name?.charAt(0) || topic.profiles?.username?.charAt(0) || "?"}
                            </div>
                          </div>
                          <div className="flex-1">
                            <div className="flex items-center gap-2 mb-1">
                              <Link
                                href={`/forum/topic/${topic.id}`}
                                className="text-lg font-semibold text-gray-800 hover:text-green-700"
                              >
                                {topic.title}
                              </Link>
                              {topic.is_hot && (
                                <span className="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">
                                  Hot
                                </span>
                              )}
                            </div>
                            <div className="flex flex-wrap items-center text-sm text-gray-500 gap-x-4 gap-y-1 mb-2">
                              <span>Oleh: {topic.profiles?.full_name || topic.profiles?.username || "Pengguna"}</span>
                              <span>Kategori: {topic.category}</span>
                              <span className="flex items-center">
                                <MessageSquare className="mr-1" size={14} />
                                {topic.replies_count || 0} balasan
                              </span>
                              <span>{topic.views || 0} dilihat</span>
                            </div>
                            <div className="flex justify-between items-center">
                              <span className="text-xs text-gray-500">
                                Aktivitas terakhir: {new Date(topic.created_at).toLocaleDateString("id-ID")}
                              </span>
                              <Link
                                href={`/forum/topic/${topic.id}`}
                                className="text-green-600 hover:text-green-700 text-sm font-medium flex items-center"
                              >
                                Lihat Diskusi
                                <ArrowRight className="ml-1" size={14} />
                              </Link>
                            </div>
                          </div>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="p-12 text-center">
                      <MessageSquare className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                      <h3 className="text-lg font-medium text-gray-800 mb-2">Belum ada topik diskusi</h3>
                      <p className="text-gray-500 mb-6">Jadilah yang pertama memulai diskusi di forum ini!</p>
                      <Link
                        href="/forum/new"
                        className="inline-flex items-center bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg"
                      >
                        <MessageSquare className="mr-2" size={18} />
                        Buat Topik Baru
                      </Link>
                    </div>
                  )}
                </div>

                {/* Pagination */}
                {forumTopics.length > 0 && (
                  <div className="bg-gray-50 p-4 border-t">
                    <div className="flex justify-center">
                      <nav className="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a
                          href="#"
                          className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                        >
                          <span className="sr-only">Sebelumnya</span>
                          <svg
                            className="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fillRule="evenodd"
                              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                              clipRule="evenodd"
                            />
                          </svg>
                        </a>
                        <a
                          href="#"
                          aria-current="page"
                          className="relative inline-flex items-center px-4 py-2 border border-green-500 bg-green-50 text-sm font-medium text-green-600 hover:bg-green-100"
                        >
                          1
                        </a>
                        <a
                          href="#"
                          className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                        >
                          <span className="sr-only">Selanjutnya</span>
                          <svg
                            className="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fillRule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clipRule="evenodd"
                            />
                          </svg>
                        </a>
                      </nav>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  )
}
