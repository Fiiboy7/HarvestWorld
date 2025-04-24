"use client"

import { useState, useEffect } from "react"
import Link from "next/link"
import { Search, ArrowRight } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"
import ImageWithFallback from "@/components/image-with-fallback"

export default function ArticlesPage() {
  const [articles, setArticles] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [activeCategory, setActiveCategory] = useState("Semua")
  const [searchQuery, setSearchQuery] = useState("")
  const [categories, setCategories] = useState(["Semua"])

  useEffect(() => {
    fetchArticles()
  }, [activeCategory])

  const fetchArticles = async () => {
    try {
      setLoading(true)
      let query = supabase
        .from("articles")
        .select("*")
        .eq("status", "approved") // Only show approved articles
        .order("created_at", { ascending: false })

      if (activeCategory !== "Semua") {
        query = query.eq("category", activeCategory)
      }

      const { data, error } = await query

      if (error) {
        throw error
      }

      // Fetch unique categories for the filter
      const { data: categoryData } = await supabase
        .from("articles")
        .select("category")
        .eq("status", "approved")
        .not("category", "is", null)

      if (categoryData) {
        const uniqueCategories = [...new Set(categoryData.map((item) => item.category).filter(Boolean))]
        setCategories(["Semua", ...uniqueCategories])
      }

      // For each article, fetch the author information separately
      const articlesWithAuthors = await Promise.all(
        data.map(async (article) => {
          if (article.author_id) {
            const { data: authorData } = await supabase
              .from("profiles")
              .select("username, full_name, avatar_url")
              .eq("id", article.author_id)
              .single()

            return { ...article, author: authorData }
          }
          return article
        }),
      )

      setArticles(articlesWithAuthors || [])
    } catch (err) {
      console.error("Error fetching articles:", err)
      setError("Gagal memuat artikel. Silakan coba lagi nanti.")
    } finally {
      setLoading(false)
    }
  }

  const handleSearch = (e) => {
    e.preventDefault()
    searchArticles(searchQuery)
  }

  const searchArticles = async (query) => {
    if (!query.trim()) {
      fetchArticles()
      return
    }

    try {
      setLoading(true)
      const { data, error } = await supabase
        .from("articles")
        .select("*")
        .eq("status", "approved")
        .or(`title.ilike.%${query}%,content.ilike.%${query}%,excerpt.ilike.%${query}%`)
        .order("created_at", { ascending: false })

      if (error) {
        throw error
      }

      // For each article, fetch the author information separately
      const articlesWithAuthors = await Promise.all(
        data.map(async (article) => {
          if (article.author_id) {
            const { data: authorData } = await supabase
              .from("profiles")
              .select("username, full_name, avatar_url")
              .eq("id", article.author_id)
              .single()

            return { ...article, author: authorData }
          }
          return article
        }),
      )

      setArticles(articlesWithAuthors || [])
    } catch (err) {
      console.error("Error searching articles:", err)
      setError("Gagal mencari artikel. Silakan coba lagi nanti.")
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      {/* Hero Section */}
      <section className="relative py-16 bg-green-700">
        <div className="absolute inset-0 z-0">
          <ImageWithFallback
            src="/placeholder.svg?height=400&width=1920&text=Artikel+Pertanian"
            alt="Artikel Pertanian"
            fill
            style={{ objectFit: "cover" }}
          />
          <div className="absolute inset-0 bg-green-900/70"></div>
        </div>

        <div className="container mx-auto px-4 relative z-10 text-white">
          <h1 className="text-4xl md:text-5xl font-bold mb-4 text-center">Artikel Pertanian</h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-center">
            Perluas pengetahuan Anda dengan artikel ahli kami tentang pertanian berkelanjutan dan perawatan tanaman
          </p>

          <div className="max-w-xl mx-auto">
            <form onSubmit={handleSearch} className="relative">
              <input
                type="text"
                placeholder="Cari artikel..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full py-3 px-4 pr-10 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500"
              />
              <button type="submit" className="absolute right-3 top-3 text-gray-500">
                <Search size={20} />
              </button>
            </form>
          </div>
        </div>
      </section>

      {/* Categories */}
      <section className="py-8 bg-white">
        <div className="container mx-auto px-4">
          <div className="flex flex-wrap justify-center gap-4">
            {categories.map((category, index) => (
              <button
                key={index}
                className={`px-4 py-2 rounded-full text-sm font-medium ${
                  activeCategory === category
                    ? "bg-green-600 text-white"
                    : "bg-green-100 text-green-800 hover:bg-green-200"
                }`}
                onClick={() => setActiveCategory(category)}
              >
                {category}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Articles Grid */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          {loading ? (
            <div className="flex justify-center items-center py-12">
              <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
            </div>
          ) : error ? (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>
          ) : articles.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {articles.map((article) => (
                <div
                  key={article.id}
                  className="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                >
                  <div className="h-48 relative">
                    <ImageWithFallback
                      src={article.image_url || ""}
                      alt={article.title}
                      fallbackSrc={`/placeholder.svg?height=200&width=400&text=${encodeURIComponent(article.title)}`}
                      fill
                      style={{ objectFit: "cover" }}
                    />
                    <div className="absolute top-4 left-4">
                      <span className="bg-green-600 text-white text-xs font-medium px-2.5 py-1 rounded">
                        {article.category || "Umum"}
                      </span>
                    </div>
                  </div>
                  <div className="p-6">
                    <div className="flex justify-between items-center text-sm text-gray-500 mb-3">
                      <span>
                        {new Date(article.created_at).toLocaleDateString("id-ID", {
                          year: "numeric",
                          month: "long",
                          day: "numeric",
                        })}
                      </span>
                      <span>{article.read_time || 5} menit baca</span>
                    </div>
                    <h3 className="text-xl font-bold mb-3 text-gray-800">{article.title}</h3>
                    <p className="text-gray-600 mb-4">{article.excerpt}</p>
                    <div className="flex justify-between items-center">
                      <span className="text-sm text-gray-500">
                        Oleh: {article.author?.full_name || article.author?.username || "Admin"}
                      </span>
                      <Link
                        href={`/articles/${article.id}`}
                        className="inline-flex items-center text-green-600 hover:text-green-700 font-medium"
                      >
                        Baca Artikel
                        <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={16} />
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">Tidak ada artikel yang ditemukan.</p>
              {searchQuery && (
                <button
                  onClick={() => {
                    setSearchQuery("")
                    setActiveCategory("Semua")
                    fetchArticles()
                  }}
                  className="mt-4 text-green-600 hover:text-green-700 font-medium"
                >
                  Lihat semua artikel
                </button>
              )}
            </div>
          )}

          {/* Pagination - can be implemented later if needed */}
          {articles.length > 0 && (
            <div className="flex justify-center mt-12">
              <nav className="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a
                  href="#"
                  className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  <span className="sr-only">Sebelumnya</span>
                  <svg className="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                  <svg className="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                      fillRule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clipRule="evenodd"
                    />
                  </svg>
                </a>
              </nav>
            </div>
          )}
        </div>
      </section>

      <Footer />
    </div>
  )
}
