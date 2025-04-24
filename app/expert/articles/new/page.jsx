"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { BookOpen, Save, ArrowLeft } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function NewArticlePage() {
  const router = useRouter()
  const { user, loading, isExpert } = useUser()
  const [formData, setFormData] = useState({
    title: "",
    slug: "",
    excerpt: "",
    content: "",
    category: "",
    image_url: "",
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [error, setError] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    if (!loading && !user) {
      router.push("/login")
      return
    }

    if (!loading && !isExpert) {
      router.push("/")
      return
    }
  }, [user, loading, isExpert, router])

  const handleChange = (e) => {
    const { name, value } = e.target

    // Auto-generate slug from title
    if (name === "title") {
      const slug = value
        .toLowerCase()
        .replace(/[^\w\s]/gi, "")
        .replace(/\s+/g, "-")

      setFormData((prev) => ({
        ...prev,
        [name]: value,
        slug,
      }))
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: value,
      }))
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setIsSubmitting(true)
    setError("")
    setSuccessMessage("")

    try {
      // Validate form
      if (!formData.title || !formData.content || !formData.category) {
        throw new Error("Judul, konten, dan kategori harus diisi")
      }

      // Submit article
      const { data, error } = await supabase
        .from("articles")
        .insert({
          ...formData,
          author_id: user.id,
          status: "pending", // All articles from experts need approval
        })
        .select()

      if (error) {
        throw error
      }

      setSuccessMessage("Artikel berhasil dikirim dan sedang menunggu persetujuan admin")

      // Reset form
      setFormData({
        title: "",
        slug: "",
        excerpt: "",
        content: "",
        category: "",
        image_url: "",
      })

      // Redirect to expert dashboard after 2 seconds
      setTimeout(() => {
        router.push("/expert/dashboard")
      }, 2000)
    } catch (err) {
      console.error("Error submitting article:", err)
      setError(err.message || "Gagal mengirim artikel. Silakan coba lagi.")
    } finally {
      setIsSubmitting(false)
    }
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-green-50">
        <Navbar />
        <div className="container mx-auto px-4 py-12">
          <div className="bg-white p-8 rounded-lg shadow-md">
            <div className="animate-pulse space-y-4">
              <div className="h-6 bg-gray-200 rounded w-1/4"></div>
              <div className="h-4 bg-gray-200 rounded w-full"></div>
              <div className="h-4 bg-gray-200 rounded w-full"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
          </div>
        </div>
        <Footer />
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      <div className="container mx-auto px-4 py-12">
        <div className="max-w-4xl mx-auto">
          <div className="flex items-center justify-between mb-6">
            <h1 className="text-3xl font-bold text-green-800">Tulis Artikel Baru</h1>
            <button onClick={() => router.back()} className="flex items-center text-green-600 hover:text-green-700">
              <ArrowLeft size={18} className="mr-1" />
              Kembali
            </button>
          </div>

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>
          )}

          {successMessage && (
            <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
              {successMessage}
            </div>
          )}

          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="p-6 bg-green-700 text-white">
              <div className="flex items-center gap-2">
                <BookOpen size={20} />
                <h2 className="text-xl font-semibold">Form Artikel</h2>
              </div>
              <p className="mt-1 text-green-100">Artikel akan ditinjau oleh admin sebelum dipublikasikan</p>
            </div>

            <form onSubmit={handleSubmit} className="p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div className="md:col-span-2">
                  <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-1">
                    Judul Artikel <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="title"
                    name="title"
                    value={formData.title}
                    onChange={handleChange}
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  />
                </div>

                <div>
                  <label htmlFor="slug" className="block text-sm font-medium text-gray-700 mb-1">
                    Slug (URL)
                  </label>
                  <input
                    type="text"
                    id="slug"
                    name="slug"
                    value={formData.slug}
                    onChange={handleChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50"
                    readOnly
                  />
                  <p className="mt-1 text-xs text-gray-500">Otomatis dibuat dari judul artikel</p>
                </div>

                <div>
                  <label htmlFor="category" className="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span className="text-red-500">*</span>
                  </label>
                  <select
                    id="category"
                    name="category"
                    value={formData.category}
                    onChange={handleChange}
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  >
                    <option value="">Pilih Kategori</option>
                    <option value="Keberlanjutan">Keberlanjutan</option>
                    <option value="Teknik Budidaya">Teknik Budidaya</option>
                    <option value="Pengendalian Hama">Pengendalian Hama</option>
                    <option value="Konservasi">Konservasi</option>
                    <option value="Tanah">Tanah</option>
                    <option value="Pertanian Perkotaan">Pertanian Perkotaan</option>
                  </select>
                </div>

                <div className="md:col-span-2">
                  <label htmlFor="excerpt" className="block text-sm font-medium text-gray-700 mb-1">
                    Ringkasan Artikel
                  </label>
                  <textarea
                    id="excerpt"
                    name="excerpt"
                    value={formData.excerpt}
                    onChange={handleChange}
                    rows={3}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Ringkasan singkat artikel (akan ditampilkan di halaman daftar artikel)"
                  ></textarea>
                </div>

                <div className="md:col-span-2">
                  <label htmlFor="content" className="block text-sm font-medium text-gray-700 mb-1">
                    Konten Artikel <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    id="content"
                    name="content"
                    value={formData.content}
                    onChange={handleChange}
                    rows={12}
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Tulis konten artikel Anda di sini..."
                  ></textarea>
                </div>

                <div>
                  <label htmlFor="image_url" className="block text-sm font-medium text-gray-700 mb-1">
                    URL Gambar
                  </label>
                  <input
                    type="url"
                    id="image_url"
                    name="image_url"
                    value={formData.image_url}
                    onChange={handleChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="https://example.com/image.jpg"
                  />
                </div>
              </div>

              <div className="flex justify-end">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 disabled:opacity-50"
                >
                  <Save size={18} />
                  {isSubmitting ? "Mengirim..." : "Kirim Artikel"}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  )
}
