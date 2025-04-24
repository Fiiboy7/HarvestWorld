"use client"

import { useState } from "react"
import Image from "next/image"
import Link from "next/link"
import { useRouter } from "next/navigation"
import { Menu, X, User, LogOut, Settings, Shield } from "lucide-react"
import { useUser } from "@/components/user-provider"

export default function Navbar() {
  const router = useRouter()
  const { user, profile, loading, isExpert, isAdmin, signOut } = useUser()
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  const handleSignOut = async () => {
    try {
      const { error } = await signOut()
      if (error) throw error

      console.log("Signed out successfully")
      router.push("/")
      router.refresh()
    } catch (err) {
      console.error("Error signing out:", err)
    }
  }

  return (
    <header className="bg-green-800 text-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 py-4 flex justify-between items-center">
        <div className="flex items-center gap-2">
          <Link href="/">
            <Image
              src="/placeholder.svg?height=40&width=40"
              alt="Logo HarvestWorld"
              width={40}
              height={40}
              className="rounded-full bg-white p-1"
            />
          </Link>
          <h1 className="text-xl md:text-2xl font-bold">HarvestWorld</h1>
        </div>

        <nav className="hidden md:flex space-x-6">
          <Link href="/" className="hover:text-green-200 font-medium">
            Beranda
          </Link>
          <Link href="/plants" className="hover:text-green-200 font-medium">
            Tanaman
          </Link>
          <Link href="/articles" className="hover:text-green-200 font-medium">
            Artikel
          </Link>
          <Link href="/forum" className="hover:text-green-200 font-medium">
            Forum
          </Link>
          <Link href="/about" className="hover:text-green-200 font-medium">
            Tentang Kami
          </Link>
        </nav>

        <div className="hidden md:flex items-center space-x-4">
          {loading ? (
            <div className="w-8 h-8 rounded-full bg-green-700 animate-pulse"></div>
          ) : user ? (
            <div className="relative group">
              <button className="flex items-center space-x-2 hover:text-green-200">
                <div className="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center overflow-hidden">
                  {profile?.avatar_url ? (
                    <Image
                      src={profile.avatar_url || "/placeholder.svg"}
                      alt="Avatar"
                      width={32}
                      height={32}
                      className="object-cover w-full h-full"
                    />
                  ) : (
                    <User size={18} />
                  )}
                </div>
                <span>{profile?.full_name || user.email?.split("@")[0]}</span>
                {isExpert && <span className="bg-green-600 text-xs px-2 py-0.5 rounded-full">Pakar</span>}
                {isAdmin && <span className="bg-purple-600 text-xs px-2 py-0.5 rounded-full">Admin</span>}
              </button>
              <div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 invisible group-hover:visible">
                <Link href="/profile" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <div className="flex items-center">
                    <User size={16} className="mr-2" />
                    <span>Profil Saya</span>
                  </div>
                </Link>

                {isExpert && (
                  <Link href="/expert/dashboard" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div className="flex items-center">
                      <Settings size={16} className="mr-2" />
                      <span>Dashboard Pakar</span>
                    </div>
                  </Link>
                )}

                {isAdmin && (
                  <Link href="/admin/users" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div className="flex items-center">
                      <Shield size={16} className="mr-2" />
                      <span>Admin Panel</span>
                    </div>
                  </Link>
                )}

                <button
                  onClick={handleSignOut}
                  className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <div className="flex items-center">
                    <LogOut size={16} className="mr-2" />
                    <span>Keluar</span>
                  </div>
                </button>
              </div>
            </div>
          ) : (
            <>
              <Link href="/login" className="hover:text-green-200 font-medium">
                Masuk
              </Link>
              <Link
                href="/signup"
                className="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-medium transition-colors"
              >
                Daftar
              </Link>
            </>
          )}
        </div>

        <button className="md:hidden text-white" onClick={() => setMobileMenuOpen(!mobileMenuOpen)}>
          {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
        </button>
      </div>

      {/* Mobile menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-green-700 py-4">
          <div className="container mx-auto px-4 flex flex-col space-y-3">
            <Link
              href="/"
              className="hover:bg-green-600 py-2 px-3 rounded font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Beranda
            </Link>
            <Link
              href="/plants"
              className="hover:bg-green-600 py-2 px-3 rounded font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Tanaman
            </Link>
            <Link
              href="/articles"
              className="hover:bg-green-600 py-2 px-3 rounded font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Artikel
            </Link>
            <Link
              href="/forum"
              className="hover:bg-green-600 py-2 px-3 rounded font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Forum
            </Link>
            <Link
              href="/about"
              className="hover:bg-green-600 py-2 px-3 rounded font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Tentang Kami
            </Link>

            <div className="border-t border-green-600 pt-3">
              {user ? (
                <>
                  <div className="flex items-center space-x-2 mb-3 px-3">
                    <div className="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center overflow-hidden">
                      {profile?.avatar_url ? (
                        <Image
                          src={profile.avatar_url || "/placeholder.svg"}
                          alt="Avatar"
                          width={32}
                          height={32}
                          className="object-cover w-full h-full"
                        />
                      ) : (
                        <User size={18} />
                      )}
                    </div>
                    <span>{profile?.full_name || user.email?.split("@")[0]}</span>
                    {isExpert && <span className="bg-green-600 text-xs px-2 py-0.5 rounded-full">Pakar</span>}
                    {isAdmin && <span className="bg-purple-600 text-xs px-2 py-0.5 rounded-full">Admin</span>}
                  </div>
                  <Link
                    href="/profile"
                    className="hover:bg-green-600 py-2 px-3 rounded font-medium block"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    <div className="flex items-center">
                      <User size={16} className="mr-2" />
                      <span>Profil Saya</span>
                    </div>
                  </Link>

                  {isExpert && (
                    <Link
                      href="/expert/dashboard"
                      className="hover:bg-green-600 py-2 px-3 rounded font-medium block"
                      onClick={() => setMobileMenuOpen(false)}
                    >
                      <div className="flex items-center">
                        <Settings size={16} className="mr-2" />
                        <span>Dashboard Pakar</span>
                      </div>
                    </Link>
                  )}

                  {isAdmin && (
                    <Link
                      href="/admin/users"
                      className="hover:bg-green-600 py-2 px-3 rounded font-medium block"
                      onClick={() => setMobileMenuOpen(false)}
                    >
                      <div className="flex items-center">
                        <Shield size={16} className="mr-2" />
                        <span>Admin Panel</span>
                      </div>
                    </Link>
                  )}

                  <button
                    onClick={() => {
                      handleSignOut()
                      setMobileMenuOpen(false)
                    }}
                    className="hover:bg-green-600 py-2 px-3 rounded font-medium block w-full text-left"
                  >
                    <div className="flex items-center">
                      <LogOut size={16} className="mr-2" />
                      <span>Keluar</span>
                    </div>
                  </button>
                </>
              ) : (
                <>
                  <Link
                    href="/login"
                    className="hover:bg-green-600 py-2 px-3 rounded font-medium block"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    Masuk
                  </Link>
                  <Link
                    href="/signup"
                    className="bg-green-600 hover:bg-green-700 py-2 px-3 rounded font-medium block mt-2"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    Daftar
                  </Link>
                </>
              )}
            </div>
          </div>
        </div>
      )}
    </header>
  )
}
