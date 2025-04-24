"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { Award, CheckCircle, Loader2 } from "lucide-react"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function RequestExpertPage() {
  const router = useRouter()
  const { user, loading } = useUser()
  const [formData, setFormData] = useState({
    expertise: "",
    experience: "",
    credentials: "",
    reason: "",
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [error, setError] = useState("")
  const [success, setSuccess] = useState(false)
  const [existingRequest, setExistingRequest] = useState(null)

  useEffect(() => {
    // Redirect to login if not logged in
    if (!loading && !user) {
      router.push("/login")
      return
    }

    // Check if user already has a pending request
    if (user) {
      checkExistingRequest()
    }
  }, [user, loading, router])

  const checkExistingRequest = async () => {
    try {
      const { data, error } = await supabase
        .from("expert_requests")
        .select("*")
        .eq("user_id", user.id)
        .order("created_at", { ascending: false })
        .limit(1)
        .single()

      if (error && error.code !== "PGRST116") {
        // PGRST116 is the error code for no rows returned
        console.error("Error checking existing request:", error)
        return
      }

      if (data) {
        setExistingRequest(data)
      }
    } catch (err) {
      console.error("Error checking existing request:", err)
    }
  }

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()

    if (isSubmitting) return

    try {
      setIsSubmitting(true)
      setError("")

      // Validate form
      if (!formData.expertise.trim()) {
        setError("Bidang keahlian harus diisi")
        setIsSubmitting(false)
        return
      }

      if (!formData.experience.trim()) {
        setError("Pengalaman harus diisi")
        setIsSubmitting(false)
        return
      }

      if (!formData.reason.trim()) {
        setError("Alasan harus diisi")
        setIsSubmitting(false)
        return
      }

      // Submit request
      const { error: insertError } = await supabase.from("expert_requests").insert([
        {
          user_id: user.id,
          expertise: formData.expertise,
          experience: formData.experience,
          credentials: formData.credentials,
          reason: formData.reason,
          status: "pending",
        },
      ])

      if (insertError) {
        throw insertError
      }

      setSuccess(true)

      // Wait 2 seconds before redirecting
      setTimeout(() => {
        router.push("/profile")
      }, 2000)
    } catch (err) {
      console.error("Error submitting expert request:", err)
      setError("Gagal mengirim permintaan: " + err.message)
      setIsSubmitting(false)
    }
  }

  if (loading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 bg-gray-200 rounded w-1/4"></div>
          <div className="h-64 bg-gray-200 rounded"></div>
        </div>
      </div>
    )
  }

  if (success) {
    return (
      <div className="max-w-2xl mx-auto p-6">
        <div className="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
          <CheckCircle className="mx-auto h-16 w-16 text-green-500 mb-4" />
          <h2 className="text-2xl font-bold text-green-800 mb-2">Permintaan Berhasil Dikirim!</h2>
          <p className="text-green-700 mb-4">
            Permintaan Anda untuk menjadi pakar tanaman telah berhasil dikirim. Admin akan meninjau permintaan Anda
            segera.
          </p>
          <p className="text-sm text-green-600">Mengalihkan ke halaman profil...</p>
        </div>
      </div>
    )
  }

  if (existingRequest) {
    return (
      <div className="max-w-2xl mx-auto p-6">
        <h1 className="text-2xl font-bold text-green-800 mb-6">Status Permintaan Pakar</h1>

        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="p-6 bg-green-700 text-white">
            <div className="flex items-center gap-2">
              <Award size={20} />
              <h2 className="text-xl font-semibold">Permintaan Anda</h2>
            </div>
          </div>

          <div className="p-6">
            <div className="mb-6">
              <div className="flex items-center mb-4">
                <div
                  className={`px-3 py-1 rounded-full text-sm font-medium ${
                    existingRequest.status === "approved"
                      ? "bg-green-100 text-green-800"
                      : existingRequest.status === "rejected"
                        ? "bg-red-100 text-red-800"
                        : "bg-yellow-100 text-yellow-800"
                  }`}
                >
                  {existingRequest.status === "approved"
                    ? "Disetujui"
                    : existingRequest.status === "rejected"
                      ? "Ditolak"
                      : "Menunggu Persetujuan"}
                </div>
              </div>

              <div className="space-y-4">
                <div>
                  <h3 className="text-sm font-medium text-gray-500">Bidang Keahlian</h3>
                  <p className="mt-1">{existingRequest.expertise}</p>
                </div>

                <div>
                  <h3 className="text-sm font-medium text-gray-500">Tanggal Pengajuan</h3>
                  <p className="mt-1">
                    {new Date(existingRequest.created_at).toLocaleDateString("id-ID", {
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                    })}
                  </p>
                </div>

                {existingRequest.status === "rejected" && existingRequest.admin_comments && (
                  <div>
                    <h3 className="text-sm font-medium text-gray-500">Alasan Penolakan</h3>
                    <p className="mt-1 text-red-600">{existingRequest.admin_comments}</p>
                  </div>
                )}
              </div>
            </div>

            <div className="flex justify-between">
              <button
                onClick={() => router.push("/profile")}
                className="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
              >
                Kembali ke Profil
              </button>

              {existingRequest.status === "rejected" && (
                <button
                  onClick={() => setExistingRequest(null)}
                  className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                >
                  Ajukan Kembali
                </button>
              )}
            </div>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-2xl mx-auto p-6">
      <h1 className="text-2xl font-bold text-green-800 mb-6">Ajukan Diri sebagai Pakar Tanaman</h1>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 bg-green-700 text-white">
          <div className="flex items-center gap-2">
            <Award size={20} />
            <h2 className="text-xl font-semibold">Formulir Pengajuan</h2>
          </div>
          <p className="mt-1 text-green-100">
            Lengkapi formulir di bawah ini untuk mengajukan diri sebagai pakar tanaman di HarvestWorld
          </p>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          <div>
            <label htmlFor="expertise" className="block text-sm font-medium text-gray-700 mb-1">
              Bidang Keahlian <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              id="expertise"
              name="expertise"
              value={formData.expertise}
              onChange={handleChange}
              placeholder="Contoh: Tanaman Hias, Tanaman Obat, Pertanian Organik"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              disabled={isSubmitting}
              required
            />
          </div>

          <div>
            <label htmlFor="experience" className="block text-sm font-medium text-gray-700 mb-1">
              Pengalaman <span className="text-red-500">*</span>
            </label>
            <textarea
              id="experience"
              name="experience"
              value={formData.experience}
              onChange={handleChange}
              placeholder="Jelaskan pengalaman Anda dalam bidang tanaman"
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              disabled={isSubmitting}
              required
            ></textarea>
          </div>

          <div>
            <label htmlFor="credentials" className="block text-sm font-medium text-gray-700 mb-1">
              Kredensial/Sertifikasi <span className="text-gray-400">(opsional)</span>
            </label>
            <textarea
              id="credentials"
              name="credentials"
              value={formData.credentials}
              onChange={handleChange}
              placeholder="Sebutkan sertifikasi, pendidikan, atau kredensial lain yang relevan"
              rows="3"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              disabled={isSubmitting}
            ></textarea>
          </div>

          <div>
            <label htmlFor="reason" className="block text-sm font-medium text-gray-700 mb-1">
              Alasan Ingin Menjadi Pakar <span className="text-red-500">*</span>
            </label>
            <textarea
              id="reason"
              name="reason"
              value={formData.reason}
              onChange={handleChange}
              placeholder="Jelaskan mengapa Anda ingin menjadi pakar tanaman di platform kami"
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              disabled={isSubmitting}
              required
            ></textarea>
          </div>

          <div className="flex justify-between">
            <button
              type="button"
              onClick={() => router.push("/profile")}
              className="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
              disabled={isSubmitting}
            >
              Batal
            </button>
            <button
              type="submit"
              className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2"
              disabled={isSubmitting}
            >
              {isSubmitting ? (
                <>
                  <Loader2 className="h-4 w-4 animate-spin" />
                  <span>Mengirim...</span>
                </>
              ) : (
                <span>Kirim Pengajuan</span>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
