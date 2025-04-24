"use client"

import { useState } from "react"
import Image from "next/image"
import Link from "next/link"
import { useRouter } from "next/navigation"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"

export default function LoginPage() {
  const router = useRouter()
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const [loading, setLoading] = useState(false)

  const handleEmailLogin = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError("")

    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
      })

      if (error) {
        console.error("Login error:", error)
        // Show warning instead of error
        setError("Email atau kata sandi yang Anda masukkan tidak valid. Silakan coba lagi.")
        return
      }

      if (data?.user) {
        console.log("Login successful, redirecting to homepage...")
        // Redirect to homepage after successful login
        router.push("/")
      }
    } catch (err) {
      console.error("Unexpected error during login:", err)
      setError("Terjadi kesalahan saat login. Silakan coba lagi.")
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-green-50 flex flex-col">
      <Navbar />

      <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-md w-full space-y-8">
          <div>
            <Image
              src="/placeholder.svg?height=60&width=60&text=HW"
              alt="HarvestWorld Logo"
              width={60}
              height={60}
              className="mx-auto h-12 w-auto rounded-full bg-green-600 p-2"
            />
            <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Masuk ke akun Anda</h2>
            <p className="mt-2 text-center text-sm text-gray-600">
              Atau{" "}
              <Link href="/signup" className="font-medium text-green-600 hover:text-green-500">
                daftar untuk akun baru
              </Link>
            </p>
          </div>

          {error && (
            <div className="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md">{error}</div>
          )}

          <div className="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form className="space-y-6" onSubmit={handleEmailLogin}>
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                  Alamat Email
                </label>
                <div className="mt-1">
                  <input
                    id="email"
                    name="email"
                    type="email"
                    autoComplete="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <div>
                <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                  Kata Sandi
                </label>
                <div className="mt-1">
                  <input
                    id="password"
                    name="password"
                    type="password"
                    autoComplete="current-password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <input
                    id="remember-me"
                    name="remember-me"
                    type="checkbox"
                    className="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                  />
                  <label htmlFor="remember-me" className="ml-2 block text-sm text-gray-900">
                    Ingat saya
                  </label>
                </div>

                <div className="text-sm">
                  <a href="#" className="font-medium text-green-600 hover:text-green-500">
                    Lupa kata sandi?
                  </a>
                </div>
              </div>

              <div>
                <button
                  type="submit"
                  disabled={loading}
                  className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                >
                  {loading ? "Memproses..." : "Masuk"}
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
