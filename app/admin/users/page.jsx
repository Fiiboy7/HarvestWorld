"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { User, Shield, CheckCircle, XCircle } from "lucide-react"
import { getUsersByRole, updateUserRole } from "@/app/actions/profile"
import { useUser } from "@/components/user-provider"
import { createClientComponentClient } from "@supabase/auth-helpers-nextjs"

export default function AdminUsersPage() {
  const router = useRouter()
  const { user, loading } = useUser()
  const supabase = createClientComponentClient()

  const [users, setUsers] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    async function checkAdminAndLoadUsers() {
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

      // Load all users
      const { users, error } = await getUsersByRole("user")

      if (error) {
        setError(error)
        setIsLoading(false)
        return
      }

      // Load experts
      const { users: experts, error: expertsError } = await getUsersByRole("expert")

      if (expertsError) {
        setError(expertsError)
        setIsLoading(false)
        return
      }

      setUsers([...users, ...experts])
      setIsLoading(false)
    }

    if (user) {
      checkAdminAndLoadUsers()
    }
  }, [user, loading, router])

  const handleRoleChange = async (userId, newRole) => {
    setError("")
    setSuccessMessage("")

    try {
      const { success, error } = await updateUserRole(userId, newRole)

      if (error) {
        setError(error)
        return
      }

      if (success) {
        // Update the user in the local state
        setUsers(users.map((u) => (u.id === userId ? { ...u, role: newRole } : u)))

        setSuccessMessage(`Berhasil mengubah peran pengguna menjadi ${newRole}`)

        // Clear success message after 3 seconds
        setTimeout(() => {
          setSuccessMessage("")
        }, 3000)
      }
    } catch (err) {
      setError("Terjadi kesalahan saat mengubah peran pengguna")
      console.error(err)
    }
  }

  if (loading || isLoading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-6 bg-gray-200 rounded w-1/4"></div>
          <div className="h-4 bg-gray-200 rounded w-full"></div>
          <div className="h-4 bg-gray-200 rounded w-full"></div>
          <div className="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    )
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6 text-green-800">Manajemen Pengguna</h1>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      {successMessage && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
          {successMessage}
        </div>
      )}

      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 bg-green-700 text-white">
          <div className="flex items-center gap-2">
            <Shield size={20} />
            <h2 className="text-xl font-semibold">Daftar Pengguna</h2>
          </div>
          <p className="mt-1 text-green-100">Kelola peran pengguna di platform</p>
        </div>

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
                  Email
                </th>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Peran
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
              {users.length > 0 ? (
                users.map((user) => (
                  <tr key={user.id}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                          {user.avatar_url ? (
                            <img
                              src={user.avatar_url || "/placeholder.svg"}
                              alt=""
                              className="h-10 w-10 rounded-full object-cover"
                            />
                          ) : (
                            <User size={20} className="text-green-600" />
                          )}
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{user.full_name || "Pengguna"}</div>
                          <div className="text-sm text-gray-500">@{user.username || "username"}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900">{user.email}</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span
                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                          user.role === "expert" ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-800"
                        }`}
                      >
                        {user.role === "expert" ? "Pakar Tanaman" : "Pengguna"}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      {user.role === "user" ? (
                        <button
                          onClick={() => handleRoleChange(user.id, "expert")}
                          className="text-green-600 hover:text-green-900 mr-4 flex items-center gap-1"
                        >
                          <CheckCircle size={16} />
                          Jadikan Pakar
                        </button>
                      ) : (
                        <button
                          onClick={() => handleRoleChange(user.id, "user")}
                          className="text-red-600 hover:text-red-900 mr-4 flex items-center gap-1"
                        >
                          <XCircle size={16} />
                          Cabut Status Pakar
                        </button>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4" className="px-6 py-4 text-center text-gray-500">
                    Tidak ada pengguna yang ditemukan
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
