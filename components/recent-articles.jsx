"use client"

import { useState, useEffect } from "react"
import Link from "next/link"
import { ArrowRight } from "lucide-react"
import { supabase } from "@/lib/supabase"
import ImageWithFallback from "@/components/image-with-fallback"

export default function RecentArticles() {
  const [articles, setArticles] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    async function fetchRecentArticles() {
      try {
        const { data, error } = await supabase
          .from("articles")
          .select("*")
          .eq("status", "approved")
          .order("created_at", { ascending: false })
          .limit(3)

        if (error) {
          throw error
        }

        // For each article, fetch the author information separately
        const articlesWithAuthors = await Promise.all(
          data.map(async (article) => {
            if (article.author_id) {
              const { data: authorData } = await supabase
                .from("profiles")
                .select("username, full_name")
                .eq("id", article.author_id)
                .single()

              return { ...article, author: authorData }
            }
            return article
          }),
        )

        setArticles(articlesWithAuthors || [])
      } catch (err) {
        console.error("Error fetching recent articles:", err)
      } finally {
        setLoading(false)
      }
    }

    fetchRecentArticles()
  }, [])

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {[1, 2, 3].map((i) => (
          <div key={i} className="bg-white rounded-xl overflow-hidden shadow-md">
            <div className="h-48 bg-gray-200 animate-pulse"></div>
            <div className="p-6">
              <div className="h-4 bg-gray-200 rounded animate-pulse mb-3"></div>
              <div className="h-6 bg-gray-200 rounded animate-pulse mb-3"></div>
              <div className="h-4 bg-gray-200 rounded animate-pulse w-3/4 mb-4"></div>
              <div className="h-4 bg-gray-200 rounded animate-pulse w-1/4"></div>
            </div>
          </div>
        ))}
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
      {articles.length > 0 ? (
        articles.map((article) => (
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
              <Link
                href={`/articles/${article.id}`}
                className="inline-flex items-center text-green-600 hover:text-green-700 font-medium"
              >
                Baca Artikel
                <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={16} />
              </Link>
            </div>
          </div>
        ))
      ) : (
        <div className="col-span-3 text-center py-8">
          <p className="text-gray-500">Belum ada artikel yang tersedia.</p>
        </div>
      )}
    </div>
  )
}
