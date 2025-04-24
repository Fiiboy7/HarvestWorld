"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import Link from "next/link"
import { Users, Leaf, BookOpen, Clock, CheckCircle } from "lucide-react"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function AdminDashboardPage() {
  const router = useRouter()
  const { user, loading, isAdmin } = useUser()
  const [stats, setStats] = useState({
    totalUsers: 0,
    totalExperts: 0,
    totalPlants: 0,
    totalArticles: 0,
    pendingArticles: 0,
  })
  const [pendingArticles, setPendingArticles] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState("")

  useEffect(() => {
    async function checkAdminAndLoadData() {
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

      loadDashboardData()
    }

    if (user) {
      checkAdminAndLoadData()
    }
  }, [user, loading, router])

  const loadDashboardData = async () => {
    try {
      setIsLoading(true)

      // Get user stats
      const { count: totalUsers } = await supabase.from("profiles").select("*", { count: "exact", head: true })

      const { count: totalExperts } = await supabase
        .from("profiles")
        .select("*", { count: "exact", head: true })
        .eq("role", "expert")

      // Get plant stats
      const { count: totalPlants } = await supabase.from("plants").select("*", { count: "exact", head: true })

      // Get article stats
      const { count: totalArticles } = await supabase.from("articles").select("*", { count: "exact", head: true })

      const { count: pendingArticles } = await supabase
        .from("articles")
        .select("*", { count: "exact", head: true })
        .eq("status", "pending")

      // Get pending articles
      const { data: pendingArticlesData, error: pendingArticlesError } = await supabase
        .from("articles")
        .select("*")
        .eq("status", "pending")
        .order("created_at", { ascending: false })
        .limit(5)

      if (pendingArticlesError) {
        throw pendingArticlesError
      }

      // For each pending article, fetch the author information separately
      const articlesWithAuthors = await Promise.all(
        (pendingArticlesData || []).map(async (article) => {
          if (article.author_id) {
            const { data: authorData } = await supabase
              .from("profiles")
              .select("username, full_name, role")
              .eq("id", article.author_id)
              .single()

            return { ...article, profiles: authorData }
          }
          return article
        }),
      )

      console.log("Pending articles:", articlesWithAuthors) // Add this for debugging

      setStats({
        totalUsers: totalUsers || 0,
        totalExperts: totalExperts || 0,
        totalPlants: totalPlants || 0,
        totalArticles: totalArticles || 0,
        pendingArticles: pendingArticles || 0,
      })

      setPendingArticles(articlesWithAuthors || [])
    } catch (err) {
      console.error("Error loading dashboard data:", err)
      setError("Gagal memuat data dashboard")
    } finally {
      setIsLoading(false)
    }
  }

  const handleApprove = async (id) => {
    try {
      const { error } = await supabase.from("articles").update({ status: "approved" }).eq("id", id)

      if (error) {
        throw error
      }

      // Refresh data
      loadDashboardData()
    } catch (err) {
      console.error("Error approving article:", err)
      setError("Gagal menyetujui artikel")
    }
  }

  const handleReject = async (id) => {
    const comments = prompt("Berikan alasan penolakan:")

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

      // Refresh data
      loadDashboardData()
    } catch (err) {
      console.error("Error rejecting article:", err)
      setError("Gagal menolak artikel")
    }
  }

  if (loading || isLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 bg-gray-200 rounded w-1/4"></div>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="h-24 bg-gray-200 rounded"></div>
            <div className="h-24 bg-gray-200 rounded"></div>
            <div className="h-24 bg-gray-200 rounded"></div>
            <div className="h-24 bg-gray-200 rounded"></div>
          </div>
          <div className="h-64 bg-gray-200 rounded"></div>
        </div>
      </div>
    )
  }

  return (
    <div>
      <h1 className="text-2xl font-bold text-green-800 mb-6">Dashboard Admin</h1>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
              <Users size={24} />
            </div>
            <div>
              <p className="text-sm text-gray-500 font-medium">Total Pengguna</p>
              <p className="text-2xl font-bold">{stats.totalUsers}</p>
            </div>
          </div>
          <div className="mt-4">
            <Link href="/admin/users" className="text-blue-600 text-sm hover:underline">
              Lihat semua pengguna
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-green-100 text-green-600 mr-4">
              <Users size={24} />
            </div>
            <div>
              <p className="text-sm text-gray-500 font-medium">Pakar Tanaman</p>
              <p className="text-2xl font-bold">{stats.totalExperts}</p>
            </div>
          </div>
          <div className="mt-4">
            <Link href="/admin/users" className="text-green-600 text-sm hover:underline">
              Kelola pakar
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-4">
              <Leaf size={24} />
            </div>
            <div>
              <p className="text-sm text-gray-500 font-medium">Total Tanaman</p>
              <p className="text-2xl font-bold">{stats.totalPlants}</p>
            </div>
          </div>
          <div className="mt-4">
            <Link href="/admin/plants" className="text-emerald-600 text-sm hover:underline">
              Kelola tanaman
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
              <BookOpen size={24} />
            </div>
            <div>
              <p className="text-sm text-gray-500 font-medium">Total Artikel</p>
              <p className="text-2xl font-bold">{stats.totalArticles}</p>
            </div>
          </div>
          <div className="mt-4">
            <Link href="/admin/articles" className="text-purple-600 text-sm hover:underline">
              Kelola artikel
            </Link>
          </div>
        </div>
      </div>

      {/* Pending Articles */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div className="p-6 bg-yellow-50 border-b border-yellow-100">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Clock className="text-yellow-600" size={20} />
              <h2 className="text-lg font-semibold text-yellow-800">
                Artikel Menunggu Persetujuan ({stats.pendingArticles})
              </h2>
            </div>
            <Link href="/admin/articles" className="text-yellow-600 text-sm hover:underline">
              Lihat semua
            </Link>
          </div>
        </div>

        <div className="p-6">
          {pendingArticles.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Judul
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
                  {pendingArticles.map((article) => (
                    <tr key={article.id}>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{article.title}</div>
                        <div className="text-xs text-gray-500">{article.category}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">
                          {article.profiles?.full_name || article.profiles?.username || "Pengguna"}
                        </div>
                        <div className="text-xs text-gray-500">
                          {article.profiles?.role === "expert" ? "Pakar Tanaman" : "Pengguna"}
                        </div>
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
                            href={`/admin/articles/preview/${article.id}`}
                            className="text-blue-600 hover:text-blue-900"
                          >
                            Pratinjau
                          </Link>
                          <button
                            onClick={() => handleApprove(article.id)}
                            className="text-green-600 hover:text-green-900"
                          >
                            Setujui
                          </button>
                          <button onClick={() => handleReject(article.id)} className="text-red-600 hover:text-red-900">
                            Tolak
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center py-8">
              <CheckCircle className="mx-auto h-12 w-12 text-green-400" />
              <h3 className="mt-2 text-sm font-medium text-gray-900">Tidak ada artikel yang menunggu persetujuan</h3>
              <p className="mt-1 text-sm text-gray-500">Semua artikel telah ditinjau.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
