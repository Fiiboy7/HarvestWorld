"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import Image from "next/image"
import Link from "next/link"
import { Leaf, Edit, Trash2, Plus, Search, Filter, Eye } from "lucide-react"
import { supabase } from "@/lib/supabase"
import { useUser } from "@/components/user-provider"

export default function AdminPlantsPage() {
  const router = useRouter()
  const { user, loading, isAdmin } = useUser()
  const [plants, setPlants] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState("")
  const [searchQuery, setSearchQuery] = useState("")
  const [categoryFilter, setCategoryFilter] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    async function checkAdminAndLoadPlants() {
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

      loadPlants()
    }

    if (user) {
      checkAdminAndLoadPlants()
    }
  }, [user, loading, router])

  const loadPlants = async () => {
    try {
      setIsLoading(true)
      let query = supabase.from("plants").select("*")

      if (searchQuery) {
        query = query.ilike("name", `%${searchQuery}%`)
      }

      if (categoryFilter) {
        query = query.eq("category", categoryFilter)
      }

      const { data, error } = await query.order("name")

      if (error) {
        console.error("Database error:", error)
        throw new Error("Failed to load plants data")
      }

      setPlants(data || [])
    } catch (err) {
      console.error("Error loading plants:", err)
      setError("Gagal memuat data tanaman: " + err.message)
    } finally {
      setIsLoading(false)
    }
  }

  const handleSearch = (e) => {
    e.preventDefault()
    loadPlants()
  }

  const handleDelete = async (id) => {
    if (!confirm("Apakah Anda yakin ingin menghapus tanaman ini?")) {
      return
    }

    try {
      const { error } = await supabase.from("plants").delete().eq("id", id)

      if (error) {
        throw error
      }

      setSuccessMessage("Tanaman berhasil dihapus")
      loadPlants()

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSuccessMessage("")
      }, 3000)
    } catch (err) {
      console.error("Error deleting plant:", err)
      setError("Gagal menghapus tanaman")
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
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-green-800">Manajemen Tanaman</h1>
        <Link
          href="/admin/plants/add"
          className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center gap-2"
        >
          <Plus size={18} />
          Tambah Tanaman
        </Link>
      </div>

      {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

      {successMessage && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
          {successMessage}
        </div>
      )}

      {/* Search and Filter */}
      <div className="bg-white p-6 rounded-lg shadow-md mb-6">
        <form onSubmit={handleSearch} className="flex flex-col md:flex-row gap-4">
          <div className="relative flex-1">
            <input
              type="text"
              placeholder="Cari tanaman..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full py-2 px-4 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <Search className="absolute right-3 top-2.5 text-gray-500" size={18} />
          </div>

          <div className="flex gap-4">
            <div className="relative">
              <select
                value={categoryFilter}
                onChange={(e) => {
                  setCategoryFilter(e.target.value)
                  // Automatically trigger search when category changes
                  setTimeout(() => loadPlants(), 100)
                }}
                className="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                <option value="">Semua Kategori</option>
                <option value="Sayuran">Sayuran</option>
                <option value="Buah">Buah</option>
                <option value="Biji-bijian">Biji-bijian</option>
                <option value="Rempah">Rempah</option>
              </select>
              <Filter className="absolute right-3 top-2.5 text-gray-500 pointer-events-none" size={18} />
            </div>

            <button type="submit" className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
              Cari
            </button>
          </div>
        </form>
      </div>

      {/* Plants Table */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 bg-green-700 text-white">
          <div className="flex items-center gap-2">
            <Leaf size={20} />
            <h2 className="text-xl font-semibold">Daftar Tanaman</h2>
          </div>
          <p className="mt-1 text-green-100">Kelola tanaman di platform</p>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Tanaman
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
                  Tingkat Kesulitan
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
              {plants.length > 0 ? (
                plants.map((plant) => (
                  <tr key={plant.id}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 relative rounded-md overflow-hidden">
                          <Image
                            src={plant.image_url || `/placeholder.svg?height=40&width=40&text=${plant.name}`}
                            alt={plant.name}
                            fill
                            style={{ objectFit: "cover" }}
                          />
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{plant.name}</div>
                          <div className="text-sm text-gray-500 italic">{plant.scientific_name}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        {plant.category || "Tidak dikategorikan"}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {plant.difficulty || "Tidak ditentukan"}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex space-x-3">
                        <Link href={`/plant/${plant.id}`} className="text-blue-600 hover:text-blue-900" title="Lihat">
                          <Eye size={18} />
                        </Link>
                        <Link
                          href={`/admin/plants/edit/${plant.id}`}
                          className="text-indigo-600 hover:text-indigo-900"
                          title="Edit"
                        >
                          <Edit size={18} />
                        </Link>
                        <button
                          onClick={() => handleDelete(plant.id)}
                          className="text-red-600 hover:text-red-900"
                          title="Hapus"
                        >
                          <Trash2 size={18} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4" className="px-6 py-4 text-center text-gray-500">
                    Tidak ada tanaman yang ditemukan
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
