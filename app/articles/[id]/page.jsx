"use client"

import { useState, useEffect } from "react"
import Link from "next/link"
import Image from "next/image"
import { ArrowLeft, Calendar, Clock, User, Send } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"
import { useRouter } from "next/navigation"
import { useUser } from "@/components/user-provider"
import ImageWithFallback from "@/components/image-with-fallback"

export default function ArticleDetailPage({ params }) {
  const router = useRouter()
  const { user, loading: userLoading } = useUser()
  const [article, setArticle] = useState(null)
  const [author, setAuthor] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [comments, setComments] = useState([])
  const [newComment, setNewComment] = useState("")
  const [isSubmittingComment, setIsSubmittingComment] = useState(false)
  const [commentError, setCommentError] = useState("")

  useEffect(() => {
    async function fetchArticle() {
      try {
        setLoading(true)
        const { data, error } = await supabase
          .from("articles")
          .select("*")
          .eq("id", params.id)
          .eq("status", "approved")
          .single()

        if (error) {
          throw error
        }

        if (!data) {
          router.push("/articles")
          return
        }

        setArticle(data)

        // Fetch author information if available
        if (data.author_id) {
          const { data: authorData, error: authorError } = await supabase
            .from("profiles")
            .select("username, full_name, avatar_url, role")
            .eq("id", data.author_id)
            .single()

          if (!authorError && authorData) {
            setAuthor(authorData)
          }
        }

        // Fetch comments for this article
        await fetchComments(params.id)
      } catch (err) {
        console.error("Error fetching article:", err)
        setError("Artikel tidak ditemukan atau telah dihapus.")
      } finally {
        setLoading(false)
      }
    }

    fetchArticle()
  }, [params.id, router])

  async function fetchComments(articleId) {
    try {
      // First, fetch the comments without trying to join with profiles
      const { data: commentsData, error: commentsError } = await supabase
        .from("article_comments")
        .select("*")
        .eq("article_id", articleId)
        .order("created_at", { ascending: true })

      if (commentsError) {
        throw commentsError
      }

      // If there are no comments, return an empty array
      if (!commentsData || commentsData.length === 0) {
        setComments([])
        return
      }

      // For each comment, fetch the associated profile separately
      const commentsWithProfiles = await Promise.all(
        commentsData.map(async (comment) => {
          try {
            // Fetch profile for this comment
            const { data: profileData, error: profileError } = await supabase
              .from("profiles")
              .select("username, full_name, avatar_url")
              .eq("id", comment.user_id)
              .single()

            if (profileError) {
              console.error(`Error fetching profile for user ${comment.user_id}:`, profileError)
              // Return comment without profile data
              return { ...comment, profiles: null }
            }

            // Return comment with profile data
            return { ...comment, profiles: profileData }
          } catch (err) {
            console.error(`Error processing comment ${comment.id}:`, err)
            // Return comment without profile data in case of any error
            return { ...comment, profiles: null }
          }
        }),
      )

      setComments(commentsWithProfiles)
    } catch (err) {
      console.error("Error fetching comments:", err)
    }
  }

  const handleCommentSubmit = async (e) => {
    e.preventDefault()

    if (!user) {
      setCommentError("Anda harus login untuk mengirim komentar")
      return
    }

    if (!newComment.trim()) {
      setCommentError("Komentar tidak boleh kosong")
      return
    }

    setIsSubmittingComment(true)
    setCommentError("")

    try {
      const { error } = await supabase.from("article_comments").insert({
        article_id: params.id,
        user_id: user.id,
        content: newComment.trim(),
      })

      if (error) throw error

      // Refresh comments
      await fetchComments(params.id)
      setNewComment("")
    } catch (err) {
      console.error("Error submitting comment:", err)
      setCommentError("Gagal mengirim komentar. Silakan coba lagi.")
    } finally {
      setIsSubmittingComment(false)
    }
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-green-50">
        <Navbar />
        <div className="container mx-auto px-4 py-12">
          <div className="max-w-4xl mx-auto">
            <div className="animate-pulse space-y-4">
              <div className="h-8 bg-gray-200 rounded w-1/2"></div>
              <div className="h-64 bg-gray-200 rounded"></div>
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

  if (error || !article) {
    return (
      <div className="min-h-screen bg-green-50">
        <Navbar />
        <div className="container mx-auto px-4 py-12">
          <div className="max-w-4xl mx-auto">
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
              {error || "Artikel tidak ditemukan."}
            </div>
            <div className="mt-6 text-center">
              <Link
                href="/articles"
                className="inline-flex items-center text-green-600 hover:text-green-700 font-medium"
              >
                <ArrowLeft className="mr-2" size={16} />
                Kembali ke Daftar Artikel
              </Link>
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
          <Link href="/articles" className="inline-flex items-center text-green-600 hover:text-green-700 mb-6">
            <ArrowLeft className="mr-2" size={16} />
            Kembali ke Daftar Artikel
          </Link>

          {/* Article Header */}
          <div className="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div className="h-64 md:h-96 relative">
              <ImageWithFallback
                src={article.image_url || ""}
                alt={article.title}
                fallbackSrc={`/placeholder.svg?height=400&width=800&text=${encodeURIComponent(article.title)}`}
                fill
                style={{ objectFit: "cover" }}
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
              <div className="absolute bottom-0 left-0 right-0 p-6 text-white">
                <div className="mb-2">
                  <span className="bg-green-600 text-white text-xs font-medium px-2.5 py-1 rounded">
                    {article.category || "Umum"}
                  </span>
                </div>
                <h1 className="text-3xl md:text-4xl font-bold mb-2">{article.title}</h1>
                <div className="flex flex-wrap items-center text-sm gap-4">
                  <div className="flex items-center">
                    <Calendar size={16} className="mr-1" />
                    <span>
                      {new Date(article.created_at).toLocaleDateString("id-ID", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                      })}
                    </span>
                  </div>
                  {article.read_time && (
                    <div className="flex items-center">
                      <Clock size={16} className="mr-1" />
                      <span>{article.read_time} menit baca</span>
                    </div>
                  )}
                  <div className="flex items-center">
                    <User size={16} className="mr-1" />
                    <span>
                      {author?.full_name || author?.username || "Admin"}
                      {author?.role === "expert" && (
                        <span className="ml-1 bg-green-100 text-green-800 text-xs font-medium px-1.5 py-0.5 rounded">
                          Pakar
                        </span>
                      )}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Article Content */}
          <div className="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8">
            {article.excerpt && (
              <div className="border-l-4 border-green-500 pl-4 italic text-gray-600 mb-6">{article.excerpt}</div>
            )}

            <div className="prose prose-lg max-w-none">
              {article.content.split("\n\n").map((paragraph, index) => {
                // Check if paragraph is a heading
                if (paragraph.startsWith("# ")) {
                  return (
                    <h1 key={index} className="text-3xl font-bold mt-8 mb-4">
                      {paragraph.replace("# ", "")}
                    </h1>
                  )
                } else if (paragraph.startsWith("## ")) {
                  return (
                    <h2 key={index} className="text-2xl font-bold mt-6 mb-3">
                      {paragraph.replace("## ", "")}
                    </h2>
                  )
                } else if (paragraph.startsWith("### ")) {
                  return (
                    <h3 key={index} className="text-xl font-bold mt-5 mb-2">
                      {paragraph.replace("### ", "")}
                    </h3>
                  )
                } else if (paragraph.startsWith("- ")) {
                  // Handle list items
                  const items = paragraph.split("\n").map((item) => item.replace("- ", ""))
                  return (
                    <ul key={index} className="list-disc pl-6 mb-4">
                      {items.map((item, i) => (
                        <li key={i} className="mb-1">
                          {item}
                        </li>
                      ))}
                    </ul>
                  )
                } else {
                  return (
                    <p key={index} className="mb-4">
                      {paragraph}
                    </p>
                  )
                }
              })}
            </div>
          </div>

          {/* Comments Section */}
          <div className="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8">
            <h2 className="text-2xl font-bold mb-6 text-green-800">Komentar</h2>

            {/* Comment List */}
            <div className="space-y-6 mb-8">
              {comments.length > 0 ? (
                comments.map((comment) => (
                  <div key={comment.id} className="flex gap-4 border-b border-gray-100 pb-6">
                    <div className="flex-shrink-0">
                      <div className="w-10 h-10 rounded-full overflow-hidden bg-green-100 flex items-center justify-center">
                        {comment.profiles?.avatar_url ? (
                          <Image
                            src={comment.profiles.avatar_url || "/placeholder.svg"}
                            alt=""
                            width={40}
                            height={40}
                            className="object-cover w-full h-full"
                          />
                        ) : (
                          <span className="text-green-600 text-xs font-bold">
                            {comment.profiles?.full_name?.charAt(0) || comment.profiles?.username?.charAt(0) || "?"}
                          </span>
                        )}
                      </div>
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center justify-between mb-1">
                        <div className="font-medium text-gray-800">
                          {comment.profiles?.full_name || comment.profiles?.username || "Pengguna"}
                        </div>
                        <div className="text-xs text-gray-500">
                          {new Date(comment.created_at).toLocaleDateString("id-ID", {
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                          })}
                        </div>
                      </div>
                      <p className="text-gray-700">{comment.content}</p>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8 text-gray-500">
                  Belum ada komentar. Jadilah yang pertama berkomentar!
                </div>
              )}
            </div>

            {/* Comment Form */}
            <div>
              <h3 className="text-lg font-semibold mb-4 text-gray-800">Tambahkan Komentar</h3>

              {!user && !userLoading && (
                <div className="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md mb-4">
                  Silakan{" "}
                  <Link href="/login" className="font-medium underline">
                    login
                  </Link>{" "}
                  untuk menambahkan komentar.
                </div>
              )}

              {commentError && (
                <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">
                  {commentError}
                </div>
              )}

              <form onSubmit={handleCommentSubmit}>
                <div className="mb-4">
                  <textarea
                    rows={4}
                    placeholder={user ? "Tulis komentar Anda di sini..." : "Login untuk menambahkan komentar"}
                    value={newComment}
                    onChange={(e) => setNewComment(e.target.value)}
                    disabled={!user || isSubmittingComment}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-100 disabled:text-gray-500"
                  ></textarea>
                </div>

                <div className="flex justify-end">
                  <button
                    type="submit"
                    disabled={!user || isSubmittingComment}
                    className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 disabled:opacity-50"
                  >
                    {isSubmittingComment ? (
                      <>
                        <div className="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white"></div>
                        <span>Mengirim...</span>
                      </>
                    ) : (
                      <>
                        <Send size={16} />
                        <span>Kirim Komentar</span>
                      </>
                    )}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  )
}
