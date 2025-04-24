"use client"

import { useState } from "react"
import Image from "next/image"
import Link from "next/link"
import { useRouter } from "next/navigation"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { signUp } from "@/app/actions/auth"

export default function SignupPage() {
  const router = useRouter()
  const [firstName, setFirstName] = useState("")
  const [lastName, setLastName] = useState("")
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [confirmPassword, setConfirmPassword] = useState("")
  const [agreeTerms, setAgreeTerms] = useState(false)
  const [error, setError] = useState("")
  const [loading, setLoading] = useState(false)

  const handleSignUp = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError("")

    // Validasi password
    if (password !== confirmPassword) {
      setError("Kata sandi dan konfirmasi kata sandi tidak cocok")
      setLoading(false)
      return
    }

    // Validasi syarat dan ketentuan
    if (!agreeTerms) {
      setError("Anda harus menyetujui Syarat dan Ketentuan untuk mendaftar")
      setLoading(false)
      return
    }

    try {
      const { user, error } = await signUp(email, password, firstName, lastName)

      if (error) {
        setError(error)
        return
      }

      if (user) {
        // Redirect ke halaman konfirmasi email atau langsung ke beranda
        router.push("/signup/success")
      }
    } catch (err) {
      setError("Terjadi kesalahan saat mendaftar. Silakan coba lagi.")
      console.error(err)
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
            <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Buat akun baru</h2>
            <p className="mt-2 text-center text-sm text-gray-600">
              Atau{" "}
              <Link href="/login" className="font-medium text-green-600 hover:text-green-500">
                masuk ke akun yang sudah ada
              </Link>
            </p>
          </div>

          {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">{error}</div>}

          <div className="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form className="space-y-6" onSubmit={handleSignUp}>
              <div className="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                <div>
                  <label htmlFor="first-name" className="block text-sm font-medium text-gray-700">
                    Nama Depan
                  </label>
                  <div className="mt-1">
                    <input
                      type="text"
                      name="first-name"
                      id="first-name"
                      autoComplete="given-name"
                      required
                      value={firstName}
                      onChange={(e) => setFirstName(e.target.value)}
                      className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label htmlFor="last-name" className="block text-sm font-medium text-gray-700">
                    Nama Belakang
                  </label>
                  <div className="mt-1">
                    <input
                      type="text"
                      name="last-name"
                      id="last-name"
                      autoComplete="family-name"
                      required
                      value={lastName}
                      onChange={(e) => setLastName(e.target.value)}
                      className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>

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
                    autoComplete="new-password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <div>
                <label htmlFor="password-confirm" className="block text-sm font-medium text-gray-700">
                  Konfirmasi Kata Sandi
                </label>
                <div className="mt-1">
                  <input
                    id="password-confirm"
                    name="password-confirm"
                    type="password"
                    autoComplete="new-password"
                    required
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <div className="flex items-center">
                <input
                  id="terms"
                  name="terms"
                  type="checkbox"
                  checked={agreeTerms}
                  onChange={(e) => setAgreeTerms(e.target.checked)}
                  className="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                />
                <label htmlFor="terms" className="ml-2 block text-sm text-gray-900">
                  Saya setuju dengan{" "}
                  <a href="#" className="font-medium text-green-600 hover:text-green-500">
                    Syarat dan Ketentuan
                  </a>{" "}
                  dan{" "}
                  <a href="#" className="font-medium text-green-600 hover:text-green-500">
                    Kebijakan Privasi
                  </a>
                </label>
              </div>

              <div>
                <button
                  type="submit"
                  disabled={loading}
                  className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                >
                  {loading ? "Memproses..." : "Daftar"}
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
