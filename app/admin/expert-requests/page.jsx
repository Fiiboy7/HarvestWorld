"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { Award, CheckCircle, XCircle, Eye } from "lucide-react"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function AdminExpertRequestsPage() {
  const router = useRouter()
  const { user, loading, isAdmin } = useUser()
  const [requests, setRequests] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    async function checkAdminAndLoadRequests() {
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

      loadRequests()
    }

    if (user) {
      checkAdminAndLoadRequests()
    }
  }, [user, loading, router])

  const loadRequests = async () => {
    try {
      setIsLoading(true)
      setError("")

      // First, fetch all expert requests
      const { data: requestsData, error: requestsError } = await supabase
        .from("expert_requests")
        .select("*")
        .order("created_at", { ascending: false })

      if (requestsError) {
        throw requestsError
      }

      // If there are no requests, set empty array and return
      if (!requestsData || requestsData.length === 0) {
        setRequests([])
        setIsLoading(false)
        return
      }

      // For each request, fetch the associated profile
      const requestsWithProfiles = await Promise.all(
        requestsData.map(async (request) => {
          try {
            // Fetch profile for this request - only select columns we know exist
            const { data: profileData, error: profileError } = await supabase
              .from("profiles")
              .select("id, username, full_name, avatar_url")
              .eq("id", request.user_id)
              .single()

            if (profileError) {
              console.error(`Error fetching profile for user ${request.user_id}:`, profileError)
              // Return request with minimal profile data
              return {
                ...request,
                profiles: {
                  id: request.user_id,
                  username: "Pengguna",
                  full_name: null,
                  avatar_url: null,
                },
              }
            }

            // Return request with profile data
            return {
              ...request,
              profiles: profileData,
            }
          } catch (err) {
            console.error(`Error processing request for user ${request.user_id}:`, err)
            // Return request with minimal profile data in case of any error
            return {
              ...request,
              profiles: {
                id: request.user_id,
                username: "Pengguna",
                full_name: null,
                avatar_url: null,
              },
            }
          }
        }),
      )

      setRequests(requestsWithProfiles)
    } catch (err) {
      console.error("Error loading expert requests:", err)
      setError("Gagal memuat data permintaan pakar: " + err.message)
    } finally {
      setIsLoading(false)
    }
  }

  const handleApprove = async (requestId, userId) => {
    try {
      // Update user role to expert
      const { error: updateError } = await supabase.from("profiles").update({ role: "expert" }).eq("id", userId)

      if (updateError) {
        throw updateError
      }

      // Update request status to approved
      const { error: requestError } = await supabase
        .from("expert_requests")
        .update({ status: "approved" })
        .eq("id", requestId)

      if (requestError) {
        throw requestError
      }

      setSuccessMessage("Permintaan pakar berhasil disetujui")
      loadRequests()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error approving expert request:", err)
      setError("Gagal menyetujui permintaan pakar: " + err.message)
    }
  }

  const handleReject = async (requestId) => {
    const reason = prompt("Berikan alasan penolakan:")

    if (reason === null) return // User cancelled

    try {
      const { error } = await supabase
        .from("expert_requests")
        .update({
          status: "rejected",
          admin_comments: reason,
        })
        .eq("id", requestId)

      if (error) {
        throw error
      }

      setSuccessMessage("Permintaan pakar berhasil ditolak")
      loadRequests()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error rejecting expert request:", err)
      setError("Gagal menolak permintaan pakar: " + err.message)
    }
  }

  if (loading || isLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 bg-gray-200 rounded w-1/4"></div>
          <div className="h-64 bg-gray-200 rounded"></div>
        </div>
      </div>
    )
  }

  return (
    <div>
      <h1 className="text-2xl font-bold text-green-800 mb-6">Permintaan Pakar Tanaman</h1>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      {successMessage && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
          {successMessage}
        </div>
      )}

      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 bg-green-700 text-white">
          <div className="flex items-center gap-2">
            <Award size={20} />
            <h2 className="text-xl font-semibold">Daftar Permintaan</h2>
          </div>
          <p className="mt-1 text-green-100">Kelola permintaan untuk menjadi pakar tanaman</p>
        </div>

        {requests.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Pengguna
                  </th>
                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Bidang Keahlian
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
                {requests.map((request) => (
                  <tr key={request.id}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                          {request.profiles?.avatar_url ? (
                            <img
                              src={request.profiles.avatar_url || "/placeholder.svg?height=40&width=40"}
                              alt=""
                              className="h-10 w-10 rounded-full object-cover"
                              onError={(e) => {
                                e.target.onerror = null
                                e.target.src = "/placeholder.svg?height=40&width=40"
                              }}
                            />
                          ) : (
                            <span className="text-green-600 text-xs font-bold">
                              {request.profiles?.full_name?.charAt(0) || request.profiles?.username?.charAt(0) || "?"}
                            </span>
                          )}
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">
                            {request.profiles?.full_name || request.profiles?.username || "Pengguna"}
                          </div>
                          <div className="text-sm text-gray-500">ID: {request.user_id.substring(0, 8)}...</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <div className="text-sm text-gray-900">{request.expertise}</div>
                      <div className="text-xs text-gray-500 truncate max-w-xs">
                        {request.experience ? request.experience.substring(0, 50) + "..." : "Tidak ada deskripsi"}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span
                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                          request.status === "approved"
                            ? "bg-green-100 text-green-800"
                            : request.status === "rejected"
                              ? "bg-red-100 text-red-800"
                              : "bg-yellow-100 text-yellow-800"
                        }`}
                      >
                        {request.status === "approved"
                          ? "Disetujui"
                          : request.status === "rejected"
                            ? "Ditolak"
                            : "Menunggu Persetujuan"}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {new Date(request.created_at).toLocaleDateString("id-ID", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                      })}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex space-x-3">
                        <button
                          onClick={() => {
                            const details = `
Bidang Keahlian: ${request.expertise}

Pengalaman:
${request.experience || "Tidak ada informasi"}

${request.credentials ? `Kredensial:\n${request.credentials}\n\n` : ""}
Alasan:
${request.reason || "Tidak ada informasi"}
                            `
                            alert(details)
                          }}
                          className="text-blue-600 hover:text-blue-900"
                          title="Lihat Detail"
                        >
                          <Eye size={18} />
                        </button>

                        {request.status === "pending" && (
                          <>
                            <button
                              onClick={() => handleApprove(request.id, request.user_id)}
                              className="text-green-600 hover:text-green-900"
                              title="Setujui"
                            >
                              <CheckCircle size={18} />
                            </button>
                            <button
                              onClick={() => handleReject(request.id)}
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
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="text-center py-8">
            <Award className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">Tidak ada permintaan pakar</h3>
            <p className="mt-1 text-sm text-gray-500">
              Belum ada pengguna yang mengajukan permintaan untuk menjadi pakar tanaman.
            </p>
          </div>
        )}
      </div>
    </div>
  )
}
