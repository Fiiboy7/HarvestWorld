import Link from "next/link"
import {
  ArrowLeft,
  Calendar,
  Droplet,
  Sun,
  Wind,
  ThermometerSun,
  Ruler,
  AlertTriangle,
  CheckCircle,
  MessageSquare,
} from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { getPlantById } from "@/app/actions/plants"
import { notFound } from "next/navigation"
import ImageWithFallback from "@/components/image-with-fallback"

export default async function PlantPage({ params }) {
  const plant = await getPlantById(params.id)

  if (!plant) {
    notFound()
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      <main className="container mx-auto px-4 py-8">
        <Link href="/plants" className="inline-flex items-center text-green-600 hover:text-green-700 mb-6">
          <ArrowLeft className="mr-2" size={16} />
          Kembali ke Tanaman
        </Link>

        {/* Hero Section */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden mb-8">
          <div className="md:flex">
            <div className="md:w-1/2">
              <div className="h-64 md:h-full relative">
                <ImageWithFallback
                  src={plant.image_url || ""}
                  alt={plant.name}
                  fallbackSrc={`/placeholder.svg?height=400&width=600&text=${encodeURIComponent(plant.name)}`}
                  fill
                  style={{ objectFit: "cover" }}
                />
              </div>
            </div>
            <div className="md:w-1/2 p-6 md:p-8">
              <div className="flex flex-wrap gap-2 mb-3">
                <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                  {plant.category}
                </span>
                <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                  Kesulitan: {plant.difficulty}
                </span>
                <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                  Iklim: {plant.climate}
                </span>
              </div>
              <h2 className="text-3xl font-bold text-green-800 mb-2">{plant.name}</h2>
              <p className="text-green-600 italic mb-4">{plant.scientific_name}</p>
              <p className="text-green-700 mb-6">{plant.long_description || plant.description}</p>
            </div>
          </div>
        </div>

        {/* Growing Conditions */}
        {plant.growing_conditions && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 className="text-2xl font-bold text-green-800 mb-6">Kondisi Pertumbuhan</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Sun className="text-yellow-500 mr-2" size={24} />
                  <h4 className="font-semibold">Sinar Matahari</h4>
                </div>
                <p>{plant.growing_conditions.sunlight}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Droplet className="text-blue-500 mr-2" size={24} />
                  <h4 className="font-semibold">Air</h4>
                </div>
                <p>{plant.growing_conditions.water}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Wind className="text-gray-500 mr-2" size={24} />
                  <h4 className="font-semibold">Tanah</h4>
                </div>
                <p>{plant.growing_conditions.soil}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <ThermometerSun className="text-red-500 mr-2" size={24} />
                  <h4 className="font-semibold">Suhu</h4>
                </div>
                <p>{plant.growing_conditions.temperature}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Ruler className="text-indigo-500 mr-2" size={24} />
                  <h4 className="font-semibold">Jarak Tanam</h4>
                </div>
                <p>{plant.growing_conditions.spacing}</p>
              </div>
            </div>
          </div>
        )}

        {/* Growing Calendar */}
        {plant.growing_calendar && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 className="text-2xl font-bold text-green-800 mb-6">Kalender Pertumbuhan</h3>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Calendar className="text-green-600 mr-2" size={24} />
                  <h4 className="font-semibold">Penanaman</h4>
                </div>
                <p>{plant.growing_calendar.planting}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Calendar className="text-green-600 mr-2" size={24} />
                  <h4 className="font-semibold">Perkecambahan</h4>
                </div>
                <p>{plant.growing_calendar.germination}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Calendar className="text-green-600 mr-2" size={24} />
                  <h4 className="font-semibold">Pertumbuhan</h4>
                </div>
                <p>{plant.growing_calendar.growth}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <div className="flex items-center mb-3">
                  <Calendar className="text-green-600 mr-2" size={24} />
                  <h4 className="font-semibold">Panen</h4>
                </div>
                <p>{plant.growing_calendar.harvest}</p>
              </div>
            </div>
          </div>
        )}

        {/* Common Problems and Tips */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
          {plant.common_problems && (
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-2xl font-bold text-green-800 mb-6">Masalah Umum</h3>
              <ul className="space-y-3">
                {plant.common_problems.map((problem, index) => (
                  <li key={index} className="flex items-start">
                    <AlertTriangle className="text-red-500 mr-2 flex-shrink-0 mt-1" size={18} />
                    <span>{problem}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {plant.tips && (
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-2xl font-bold text-green-800 mb-6">Tips Budidaya</h3>
              <ul className="space-y-3">
                {plant.tips.map((tip, index) => (
                  <li key={index} className="flex items-start">
                    <CheckCircle className="text-green-500 mr-2 flex-shrink-0 mt-1" size={18} />
                    <span>{tip}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>

        {/* Related Plants */}
        {plant.related_plants && plant.related_plants.length > 0 && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 className="text-2xl font-bold text-green-800 mb-6">Tanaman Terkait</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              {plant.related_plants.map((relatedPlant) => (
                <Link
                  key={relatedPlant.id}
                  href={`/plant/${relatedPlant.id}`}
                  className="bg-green-50 p-4 rounded-lg hover:bg-green-100 transition-colors"
                >
                  <h4 className="font-semibold text-lg mb-1">{relatedPlant.name}</h4>
                  <p className="text-sm text-green-700">{relatedPlant.category}</p>
                </Link>
              ))}
            </div>
          </div>
        )}

        {/* Comments Section - Empty Placeholder */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="flex items-center gap-2 mb-6">
            <MessageSquare className="text-green-600" size={24} />
            <h3 className="text-2xl font-bold text-green-800">Komentar</h3>
          </div>

          <div className="text-center py-8 bg-gray-50 rounded-lg">
            <MessageSquare className="mx-auto h-12 w-12 text-gray-300" />
            <p className="mt-2 text-gray-500">Belum ada komentar untuk tanaman ini.</p>
            <p className="text-sm text-gray-400 mt-1">Jadilah yang pertama memberikan komentar!</p>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  )
}
