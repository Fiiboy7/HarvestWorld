"use client"

import { useState, useEffect } from "react"
import Link from "next/link"
import { useRouter } from "next/navigation"
import { BookOpen, MessageSquare, PlusCircle, Users, Clock, CheckCircle, XCircle, AlertCircle } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function ExpertDashboardPage() {
  const router = useRouter()
  const { user, loading, isExpert } = useUser()
  const [articles, setArticles] = useState([])
  const [isLoadingArticles, setIsLoadingArticles] = useState(true)
  const [error, setError] = useState("")

  useEffect(() => {
    if (!loading && !user) {
      router.push("/login")
      return
    }

    if (!loading && !isExpert) {
      router.push("/")
      return
    }

    if (user) {
      loadArticles()
    }
  }, [user, loading, isExpert, router])

  const loadArticles = async () => {
    try {
      setIsLoadingArticles(true)
      const { data, error } = await supabase
        .from("articles")
        .select("*")
        .eq("author_id", user.id)
        .order("created_at", { ascending: false })

      if (error) {
        throw error
      }

      setArticles(data || [])
    } catch (err) {
      console.error("Error loading articles:", err)
      setError("Gagal memuat data artikel")
    } finally {
      setIsLoadingArticles(false)
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
        <div className="max-w-6xl mx-auto">
          <h1 className="text-3xl font-bold mb-6 text-green-800">Dashboard Pakar Tanaman</h1>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center mb-4">
                <div className="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                  <BookOpen className="text-green-600" size={24} />
                </div>
                <div>
                  <h3 className="text-lg font-semibold">Artikel</h3>
                  <p className="text-gray-500">{articles.length} artikel ditulis</p>
                </div>
              </div>
              <Link
                href="/expert/articles/new"
                className="text-green-600 hover:text-green-700 font-medium flex items-center"
              >
                <PlusCircle size={16} className="mr-1" />
                Tulis Artikel Baru
              </Link>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center mb-4">
                <div className="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                  <MessageSquare className="text-green-600" size={24} />
                </div>
                <div>
                  <h3 className="text-lg font-semibold">Forum</h3>
                  <p className="text-gray-500">0 topik dijawab</p>
                </div>
              </div>
              <Link href="/forum" className="text-green-600 hover:text-green-700 font-medium flex items-center">
                Lihat Forum
              </Link>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center mb-4">
                <div className="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                  <Users className="text-green-600" size={24} />
                </div>
                <div>
                  <h3 className="text-lg font-semibold">Komunitas</h3>
                  <p className="text-gray-500">0 pengguna dibantu</p>
                </div>
              </div>
              <Link
                href="/expert/community"
                className="text-green-600 hover:text-green-700 font-medium flex items-center"
              >
                Lihat Pertanyaan
              </Link>
            </div>
          </div>

          {/* Articles Section */}
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 className="text-xl font-bold mb-4">Artikel Saya</h2>

            {error && (
              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">{error}</div>
            )}

            {isLoadingArticles ? (
              <div className="animate-pulse space-y-4">
                <div className="h-12 bg-gray-200 rounded"></div>
                <div className="h-12 bg-gray-200 rounded"></div>
                <div className="h-12 bg-gray-200 rounded"></div>
              </div>
            ) : articles.length > 0 ? (
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
                        Kategori
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
                    {articles.map((article) => (
                      <tr key={article.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{article.title}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {article.category || "Tidak dikategorikan"}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            {article.status === "approved" ? (
                              <>
                                <CheckCircle className="text-green-500 mr-1" size={16} />
                                <span className="text-green-800">Disetujui</span>
                              </>
                            ) : article.status === "rejected" ? (
                              <>
                                <XCircle className="text-red-500 mr-1" size={16} />
                                <span className="text-red-800">Ditolak</span>
                              </>
                            ) : (
                              <>
                                <Clock className="text-yellow-500 mr-1" size={16} />
                                <span className="text-yellow-800">Menunggu Persetujuan</span>
                              </>
                            )}
                          </div>
                          {article.status === "rejected" && article.admin_comments && (
                            <div className="mt-1 text-xs text-red-600">
                              <span className="font-semibold">Alasan:</span> {article.admin_comments}
                            </div>
                          )}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {new Date(article.created_at).toLocaleDateString("id-ID", {
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                          })}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <Link
                            href={`/expert/articles/edit/${article.id}`}
                            className={`text-indigo-600 hover:text-indigo-900 ${
                              article.status === "approved" ? "pointer-events-none opacity-50" : ""
                            }`}
                          >
                            {article.status === "approved" ? "Dipublikasikan" : "Edit"}
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="text-center py-8">
                <AlertCircle className="mx-auto h-12 w-12 text-gray-400" />
                <h3 className="mt-2 text-sm font-medium text-gray-900">Belum ada artikel</h3>
                <p className="mt-1 text-sm text-gray-500">Mulai tulis artikel pertama Anda sebagai pakar tanaman.</p>
                <div className="mt-6">
                  <Link
                    href="/expert/articles/new"
                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                  >
                    <PlusCircle className="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                    Tulis Artikel Baru
                  </Link>
                </div>
              </div>
            )}
          </div>

          {/* Activity Section */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-bold mb-4">Aktivitas Terbaru</h2>

            <div className="border-t border-gray-200 pt-4">
              <p className="text-gray-500 text-center py-4">Belum ada aktivitas terbaru.</p>
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  )
}
