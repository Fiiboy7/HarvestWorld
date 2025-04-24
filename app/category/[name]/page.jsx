"use client"

import { useState, useEffect } from "react"
import Image from "next/image"
import Link from "next/link"
import { ArrowLeft } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { supabase } from "@/lib/supabase"

export default function CategoryPage({ params }) {
  const [plants, setPlants] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const categoryName = decodeURIComponent(params.name)

  useEffect(() => {
    async function fetchPlantsByCategory() {
      try {
        setLoading(true)

        // Map URL parameter to actual category name in database
        let dbCategory = categoryName
        if (categoryName.toLowerCase() === "sayuran") dbCategory = "Sayuran"
        if (categoryName.toLowerCase() === "buah-buahan") dbCategory = "Buah"
        if (categoryName.toLowerCase() === "biji-bijian") dbCategory = "Biji-bijian"
        if (categoryName.toLowerCase() === "rempah-rempah") dbCategory = "Rempah"

        const { data, error } = await supabase.from("plants").select("*").eq("category", dbCategory).order("name")

        if (error) {
          throw error
        }

        setPlants(data || [])
      } catch (err) {
        console.error("Error fetching plants by category:", err)
        setError("Gagal memuat data tanaman. Silakan coba lagi nanti.")
      } finally {
        setLoading(false)
      }
    }

    fetchPlantsByCategory()
  }, [categoryName])

  // Function to get proper category display name
  const getCategoryDisplayName = () => {
    const name = categoryName.toLowerCase()
    if (name === "sayuran") return "Sayuran"
    if (name === "buah-buahan") return "Buah-buahan"
    if (name === "biji-bijian") return "Biji-bijian"
    if (name === "rempah-rempah") return "Rempah-rempah"
    return categoryName
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      <div className="container mx-auto px-4 py-8">
        <Link href="/" className="inline-flex items-center text-green-600 hover:text-green-700 mb-6">
          <ArrowLeft className="mr-2" size={16} />
          Kembali ke Beranda
        </Link>

        <h1 className="text-3xl font-bold mb-6 text-green-800">Kategori: {getCategoryDisplayName()}</h1>

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
                    <Image
                      src={plant.image_url || `/placeholder.svg?height=200&width=300&text=${plant.name}`}
                      alt={plant.name}
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
                <p className="text-gray-500 text-lg">Tidak ada tanaman yang ditemukan dalam kategori ini.</p>
              </div>
            )}
          </div>
        )}
      </div>

      <Footer />
    </div>
  )
}
