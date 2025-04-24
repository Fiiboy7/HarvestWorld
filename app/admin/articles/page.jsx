"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import Image from "next/image"
import Link from "next/link"
import { BookOpen, Edit, Trash2, Plus, Search, Filter, Eye, CheckCircle, XCircle } from "lucide-react"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function AdminArticlesPage() {
  const router = useRouter()
  const { user, loading, isAdmin } = useUser()
  const [articles, setArticles] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState("")
  const [searchQuery, setSearchQuery] = useState("")
  const [statusFilter, setStatusFilter] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    async function checkAdminAndLoadArticles() {
      if (!loading && !user) {
        router.push("/login")
        return
      }

      // Check if user is admin
      const { data } = await supabase.from("profiles").select("role").eq("id", user.id).single()

      if (!data || data.role !== "admin") {
        router.push("/")
        return
      }

      loadArticles()
    }

    if (user) {
      checkAdminAndLoadArticles()
    }
  }, [user, loading, router])

  const loadArticles = async () => {
    try {
      setIsLoading(true)
      let query = supabase.from("articles").select("*").order("created_at", { ascending: false })

      if (searchQuery) {
        query = query.ilike("title", `%${searchQuery}%`)
      }

      if (statusFilter) {
        query = query.eq("status", statusFilter)
      }

      const { data, error } = await query

      if (error) {
        console.error("Database error:", error)
        throw new Error("Failed to load articles data")
      }

      // For each article, fetch the author information separately
      const articlesWithAuthors = await Promise.all(
        data.map(async (article) => {
          if (article.author_id) {
            try {
              const { data: authorData, error: authorError } = await supabase
                .from("profiles")
                .select("username, full_name, avatar_url, role")
                .eq("id", article.author_id)
                .single()

              if (authorError) throw authorError
              return { ...article, profiles: authorData }
            } catch (err) {
              console.error("Error fetching author:", err)
              return { ...article, profiles: null }
            }
          }
          return article
        }),
      )

      setArticles(articlesWithAuthors || [])
    } catch (err) {
      console.error("Error loading articles:", err)
      setError("Gagal memuat data artikel: " + err.message)
    } finally {
      setIsLoading(false)
    }
  }

  const handleSearch = (e) => {
    e.preventDefault()
    loadArticles()
  }

  const handleDelete = async (id) => {
    if (!confirm("Apakah Anda yakin ingin menghapus artikel ini?")) {
      return
    }

    try {
      const { error } = await supabase.from("articles").delete().eq("id", id)

      if (error) {
        throw error
      }

      setSuccessMessage("Artikel berhasil dihapus")
      loadArticles()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error deleting article:", err)
      setError("Gagal menghapus artikel")
    }
  }

  const handleApprove = async (id) => {
    try {
      const { error } = await supabase.from("articles").update({ status: "approved" }).eq("id", id)

      if (error) {
        throw error
      }

      setSuccessMessage("Artikel berhasil disetujui")
      loadArticles()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error approving article:", err)
      setError("Gagal menyetujui artikel")
    }
  }

  const handleReject = async (id, reason) => {
    const comments = prompt("Berikan alasan penolakan:", reason || "")

    if (comments === null) return // User cancelled

    try {
      const { error } = await supabase
        .from("articles")
        .update({
          status: "rejected",
          admin_comments: comments,
        })
        .eq("id", id)

      if (error) {
        throw error
      }

      setSuccessMessage("Artikel berhasil ditolak")
      loadArticles()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error rejecting article:", err)
      setError("Gagal menolak artikel")
    }
  }

  if (loading || isLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-6 bg-gray-200 rounded w-1/4"></div>
          <div className="h-4 bg-gray-200 rounded w-full"></div>
          <div className="h-4 bg-gray-200 rounded w-full"></div>
          <div className="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    )
  }

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-green-800">Manajemen Artikel</h1>
        <Link
          href="/admin/articles/add"
          className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center gap-2"
        >
          <Plus size={18} />
          Tambah Artikel
        </Link>
      </div>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      {successMessage && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
          {successMessage}
        </div>
      )}

      {/* Search and Filter */}
      <div className="bg-white p-6 rounded-lg shadow-md mb-6">
        <form onSubmit={handleSearch} className="flex flex-col md:flex-row gap-4">
          <div className="relative flex-1">
            <input
              type="text"
              placeholder="Cari artikel..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full py-2 px-4 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <Search className="absolute right-3 top-2.5 text-gray-500" size={18} />
          </div>

          <div className="flex gap-4">
            <div className="relative">
              <select
                value={statusFilter}
                onChange={(e) => {
                  setStatusFilter(e.target.value)
                  // Automatically trigger search when status changes
                  setTimeout(() => loadArticles(), 100)
                }}
                className="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                <option value="">Semua Status</option>
                <option value="pending">Menunggu Persetujuan</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
              </select>
              <Filter className="absolute right-3 top-2.5 text-gray-500 pointer-events-none" size={18} />
            </div>

            <button type="submit" className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
              Filter
            </button>
          </div>
        </form>
      </div>

      {/* Articles Table */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 bg-green-700 text-white">
          <div className="flex items-center gap-2">
            <BookOpen size={20} />
            <h2 className="text-xl font-semibold">Daftar Artikel</h2>
          </div>
          <p className="mt-1 text-green-100">Kelola artikel di platform</p>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Artikel
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Penulis
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tanggal
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tindakan
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {articles.length > 0 ? (
                articles.map((article) => (
                  <tr key={article.id}>
                    <td className="px-6 py-4">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 relative rounded-md overflow-hidden">
                          <Image
                            src={article.image_url || `/placeholder.svg?height=40&width=40&text=${article.title}`}
                            alt={article.title}
                            fill
                            style={{ objectFit: "cover" }}
                          />
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{article.title}</div>
                          <div className="text-xs text-gray-500">{article.category || "Tidak dikategorikan"}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center overflow-hidden">
                          {article.profiles?.avatar_url ? (
                            <Image
                              src={article.profiles.avatar_url || "/placeholder.svg"}
                              alt=""
                              width={32}
                              height={32}
                              className="h-8 w-8 rounded-full object-cover"
                            />
                          ) : (
                            <span className="text-green-600 text-xs font-bold">
                              {article.profiles?.full_name?.charAt(0) || article.profiles?.username?.charAt(0) || "?"}
                            </span>
                          )}
                        </div>
                        <div className="ml-3">
                          <div className="text-sm font-medium text-gray-900">
                            {article.profiles?.full_name || article.profiles?.username || "Pengguna"}
                          </div>
                          <div className="text-xs text-gray-500">
                            {article.profiles?.role === "expert" ? "Pakar Tanaman" : "Pengguna"}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span
                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                          article.status === "approved"
                            ? "bg-green-100 text-green-800"
                            : article.status === "rejected"
                              ? "bg-red-100 text-red-800"
                              : "bg-yellow-100 text-yellow-800"
                        }`}
                      >
                        {article.status === "approved"
                          ? "Disetujui"
                          : article.status === "rejected"
                            ? "Ditolak"
                            : "Menunggu Persetujuan"}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {new Date(article.created_at).toLocaleDateString("id-ID", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                      })}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex space-x-3">
                        <Link
                          href={`/articles/${article.id}`}
                          className="text-blue-600 hover:text-blue-900"
                          title="Lihat"
                        >
                          <Eye size={18} />
                        </Link>
                        <Link
                          href={`/admin/articles/edit/${article.id}`}
                          className="text-indigo-600 hover:text-indigo-900"
                          title="Edit"
                        >
                          <Edit size={18} />
                        </Link>
                        <button
                          onClick={() => handleDelete(article.id)}
                          className="text-red-600 hover:text-red-900"
                          title="Hapus"
                        >
                          <Trash2 size={18} />
                        </button>

                        {article.status === "pending" && (
                          <>
                            <button
                              onClick={() => handleApprove(article.id)}
                              className="text-green-600 hover:text-green-900"
                              title="Setujui"
                            >
                              <CheckCircle size={18} />
                            </button>
                            <button
                              onClick={() => handleReject(article.id, article.admin_comments)}
                              className="text-red-600 hover:text-red-900"
                              title="Tolak"
                            >
                              <XCircle size={18} />
                            </button>
                          </>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="5" className="px-6 py-4 text-center text-gray-500">
                    Tidak ada artikel yang ditemukan
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
