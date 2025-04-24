"use client"

import { useEffect } from "react"
import { useRouter, usePathname } from "next/navigation"
import Link from "next/link"
import { Shield, Users, Leaf, BookOpen, Award } from "lucide-react"
import { useUser } from "@/components/user-provider"

export default function AdminLayout({ children }) {
  const router = useRouter()
  const pathname = usePathname()
  const { user, loading, isAdmin } = useUser()

  useEffect(() => {
    if (!loading && !user) {
      router.push("/login")
      return
    }

    if (!loading && !isAdmin) {
      router.push("/")
      return
    }
  }, [user, loading, isAdmin, router])

  if (loading) {
    return (
      <div className="min-h-screen bg-green-50 flex flex-col">
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
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-green-50 flex flex-col">
      <div className="container mx-auto px-4 py-8">
        <div className="flex flex-col md:flex-row gap-8">
          {/* Sidebar */}
          <div className="md:w-1/4 lg:w-1/5">
            <div className="bg-white rounded-lg shadow-md overflow-hidden">
              <div className="p-4 bg-green-700 text-white">
                <div className="flex items-center gap-2">
                  <Shield size={20} />
                  <h2 className="text-lg font-semibold">Admin Panel</h2>
                </div>
              </div>
              <nav className="p-4">
                <ul className="space-y-2">
                  <li>
                    <Link
                      href="/admin"
                      className={`flex items-center gap-2 p-2 rounded-md ${
                        pathname === "/admin" ? "bg-green-100 text-green-800" : "text-gray-700 hover:bg-gray-100"
                      }`}
                    >
                      <Shield size={18} />
                      <span>Dashboard</span>
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/admin/users"
                      className={`flex items-center gap-2 p-2 rounded-md ${
                        pathname === "/admin/users" ? "bg-green-100 text-green-800" : "text-gray-700 hover:bg-gray-100"
                      }`}
                    >
                      <Users size={18} />
                      <span>Manajemen Pengguna</span>
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/admin/expert-requests"
                      className={`flex items-center gap-2 p-2 rounded-md ${
                        pathname === "/admin/expert-requests"
                          ? "bg-green-100 text-green-800"
                          : "text-gray-700 hover:bg-gray-100"
                      }`}
                    >
                      <Award size={18} />
                      <span>Permintaan Pakar</span>
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/admin/plants"
                      className={`flex items-center gap-2 p-2 rounded-md ${
                        pathname === "/admin/plants" || pathname.startsWith("/admin/plants/")
                          ? "bg-green-100 text-green-800"
                          : "text-gray-700 hover:bg-gray-100"
                      }`}
                    >
                      <Leaf size={18} />
                      <span>Manajemen Tanaman</span>
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/admin/articles"
                      className={`flex items-center gap-2 p-2 rounded-md ${
                        pathname === "/admin/articles" || pathname.startsWith("/admin/articles/")
                          ? "bg-green-100 text-green-800"
                          : "text-gray-700 hover:bg-gray-100"
                      }`}
                    >
                      <BookOpen size={18} />
                      <span>Manajemen Artikel</span>
                    </Link>
                  </li>
                </ul>
              </nav>
            </div>
          </div>

          {/* Main Content */}
          <div className="md:w-3/4 lg:w-4/5">{children}</div>
        </div>
      </div>
    </div>
  )
}
