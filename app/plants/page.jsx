"use client"

import { useState, useEffect } from "react"
import Link from "next/link"
import { Search, ChevronDown } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"
import ImageWithFallback from "@/components/image-with-fallback"

export default function PlantsPage() {
  const [plants, setPlants] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [query, setQuery] = useState("")
  const [category, setCategory] = useState("")

  useEffect(() => {
    async function fetchPlants() {
      try {
        setLoading(true)
        let plantsQuery = supabase.from("plants").select("*")

        if (query) {
          plantsQuery = plantsQuery.ilike("name", `%${query}%`)
        }

        if (category) {
          plantsQuery = plantsQuery.eq("category", category)
        }

        const { data, error } = await plantsQuery.order("name")

        if (error) {
          throw error
        }

        setPlants(data || [])
      } catch (err) {
        console.error("Error fetching plants:", err)
        setError("Gagal memuat data tanaman. Silakan coba lagi nanti.")
      } finally {
        setLoading(false)
      }
    }

    fetchPlants()
  }, [query, category])

  const handleSearch = (e) => {
    e.preventDefault()
    const formData = new FormData(e.target)
    setQuery(formData.get("query") || "")
    setCategory(formData.get("category") || "")
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      {/* Page Content */}
      <div className="container mx-auto px-4 py-8">
        <h1 className="text-3xl font-bold mb-6 text-green-800">Database Tanaman</h1>

        {/* Search and Filter */}
        <div className="bg-white p-6 rounded-lg shadow-md mb-8">
          <form className="flex flex-col md:flex-row gap-4 mb-6" onSubmit={handleSearch}>
            <div className="relative flex-1">
              <input
                type="text"
                name="query"
                placeholder="Cari tanaman..."
                defaultValue={query}
                className="w-full py-3 px-4 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
              <button type="submit" className="absolute right-3 top-3 text-gray-500">
                <Search size={20} />
              </button>
            </div>

            <div className="relative">
              <select
                name="category"
                defaultValue={category}
                className="appearance-none flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 py-3 px-4 pr-8 rounded-lg border border-green-200"
                onChange={(e) => {
                  setCategory(e.target.value)
                  e.target.form.dispatchEvent(new Event("submit", { cancelable: true, bubbles: true }))
                }}
              >
                <option value="">Semua Kategori</option>
                <option value="Sayuran">Sayuran</option>
                <option value="Buah">Buah</option>
                <option value="Biji-bijian">Biji-bijian</option>
                <option value="Rempah">Rempah</option>
              </select>
              <ChevronDown className="absolute right-3 top-3 text-green-700 pointer-events-none" size={16} />
            </div>
          </form>

          {/* Filter Tags */}
          {(query || category) && (
            <div className="flex flex-wrap gap-2">
              {query && (
                <span className="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
                  Pencarian: {query}
                  <button onClick={() => setQuery("")} className="ml-1 text-green-800 hover:text-green-900">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className="h-4 w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </span>
              )}
              {category && (
                <span className="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
                  Kategori: {category}
                  <button onClick={() => setCategory("")} className="ml-1 text-green-800 hover:text-green-900">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className="h-4 w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </span>
              )}
              <button
                onClick={() => {
                  setQuery("")
                  setCategory("")
                }}
                className="text-green-600 text-sm font-medium hover:text-green-700"
              >
                Hapus semua filter
              </button>
            </div>
          )}
        </div>

        {/* Loading State */}
        {loading && (
          <div className="flex justify-center items-center py-12">
            <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
          </div>
        )}

        {/* Error State */}
        {error && <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>}

        {/* Plants Grid */}
        {!loading && !error && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {plants.length > 0 ? (
              plants.map((plant) => (
                <div
                  key={plant.id}
                  className="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                >
                  <div className="h-48 relative">
                    <ImageWithFallback
                      src={plant.image_url || ""}
                      alt={plant.name}
                      fallbackSrc={`/placeholder.svg?height=200&width=300&text=${encodeURIComponent(plant.name)}`}
                      fill
                      style={{ objectFit: "cover" }}
                    />
                  </div>
                  <div className="p-6">
                    <div className="flex justify-between items-center mb-3">
                      <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        {plant.category || "Tanaman"}
                      </span>
                      {plant.difficulty && (
                        <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                          Kesulitan: {plant.difficulty}
                        </span>
                      )}
                    </div>
                    <h3 className="text-xl font-semibold mb-1 text-green-800">{plant.name}</h3>
                    {plant.scientific_name && (
                      <p className="text-sm text-green-600 italic mb-3">{plant.scientific_name}</p>
                    )}
                    <p className="text-green-700 mb-4">{plant.description}</p>
                    <Link
                      href={`/plant/${plant.id}`}
                      className="inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-medium transition-colors"
                    >
                      Pelajari Lebih Lanjut
                    </Link>
                  </div>
                </div>
              ))
            ) : (
              <div className="col-span-3 text-center py-12">
                <p className="text-gray-500 text-lg">Tidak ada tanaman yang ditemukan.</p>
                <button
                  onClick={() => {
                    setQuery("")
                    setCategory("")
                  }}
                  className="text-green-600 hover:text-green-700 font-medium mt-2 inline-block"
                >
                  Lihat semua tanaman
                </button>
              </div>
            )}
          </div>
        )}
      </div>

      <Footer />
    </div>
  )
}
