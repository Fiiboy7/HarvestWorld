"use client"

import { useState, useEffect } from "react"
import Image from "next/image"
import Link from "next/link"
import { useRouter } from "next/navigation"
import { Edit, Save, Upload, User, Award } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { useUser } from "@/components/user-provider"
import { supabase } from "@/lib/supabase"

export default function ProfilePage() {
  const router = useRouter()
  const { user, loading, isExpert } = useUser()

  const [profile, setProfile] = useState(null)
  const [expertRequest, setExpertRequest] = useState(null)
  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [isUploading, setIsUploading] = useState(false)
  const [error, setError] = useState("")
  const [formData, setFormData] = useState({
    username: "",
    full_name: "",
    bio: "",
    website: "",
  })
  const [pageLoading, setPageLoading] = useState(true)

  useEffect(() => {
    if (!loading && !user) {
      router.push("/login")
      return
    }

    async function loadProfile() {
      try {
        setPageLoading(true)

        // Get user profile
        const { data: profileData, error: profileError } = await supabase
          .from("profiles")
          .select("*")
          .eq("id", user.id)
          .single()

        if (profileError) {
          console.error("Error fetching profile:", profileError)
          setError("Gagal memuat profil pengguna")
          setPageLoading(false)
          return
        }

        setProfile(profileData)
        setFormData({
          username: profileData.username || "",
          full_name: profileData.full_name || "",
          bio: profileData.bio || "",
          website: profileData.website || "",
        })

        // Check if user has a pending expert request
        const { data: requestData, error: requestError } = await supabase
          .from("expert_requests")
          .select("*")
          .eq("user_id", user.id)
          .order("created_at", { ascending: false })
          .limit(1)

        if (!requestError && requestData && requestData.length > 0) {
          setExpertRequest(requestData[0])
        }
      } catch (err) {
        console.error("Error loading profile data:", err)
        setError("Terjadi kesalahan saat memuat data profil")
      } finally {
        setPageLoading(false)
      }
    }

    if (user) {
      loadProfile()
    }
  }, [user, loading, router])

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setIsSaving(true)
    setError("")

    try {
      const { error: updateError } = await supabase.from("profiles").update(formData).eq("id", user.id)

      if (updateError) {
        throw updateError
      }

      // Refresh profile data
      const { data: updatedProfile, error: fetchError } = await supabase
        .from("profiles")
        .select("*")
        .eq("id", user.id)
        .single()

      if (fetchError) {
        throw fetchError
      }

      setProfile(updatedProfile)
      setIsEditing(false)

      // Show success message or toast here if needed
    } catch (err) {
      setError("Terjadi kesalahan saat menyimpan profil")
      console.error(err)
    } finally {
      setIsSaving(false)
    }
  }

  const handleAvatarUpload = async (e) => {
    const file = e.target.files[0]
    if (!file) return

    setIsUploading(true)
    setError("")

    try {
      // Generate a unique file name
      const fileName = `avatars/${user.id}-${Date.now()}`
      const fileExt = file.name.split(".").pop()
      const fullPath = `${fileName}.${fileExt}`

      // Upload the file to Supabase Storage using the "media" bucket
      const { error: uploadError } = await supabase.storage.from("media").upload(fullPath, file, {
        cacheControl: "3600",
        upsert: true,
      })

      if (uploadError) {
        throw new Error(`Error uploading avatar: ${uploadError.message}`)
      }

      // Get the public URL
      const { data: urlData } = supabase.storage.from("media").getPublicUrl(fullPath)

      if (!urlData || !urlData.publicUrl) {
        throw new Error("Failed to get public URL for uploaded avatar")
      }

      // Update the user profile with the new avatar URL
      const { error: updateError } = await supabase
        .from("profiles")
        .update({ avatar_url: urlData.publicUrl })
        .eq("id", user.id)

      if (updateError) {
        throw new Error(`Error updating profile: ${updateError.message}`)
      }

      // Refresh profile data
      const { data: updatedProfile, error: fetchError } = await supabase
        .from("profiles")
        .select("*")
        .eq("id", user.id)
        .single()

      if (fetchError) {
        throw new Error(`Error fetching updated profile: ${fetchError.message}`)
      }

      setProfile(updatedProfile)
    } catch (err) {
      console.error("Avatar upload error:", err)
      setError(`Terjadi kesalahan saat mengunggah avatar: ${err.message}`)
    } finally {
      setIsUploading(false)
    }
  }

  if (loading || pageLoading) {
    return (
      <div className="min-h-screen bg-green-50">
        <Navbar />
        <div className="container mx-auto px-4 py-12">
          <div className="bg-white p-8 rounded-lg shadow-md">
            <div className="animate-pulse flex flex-col items-center">
              <div className="rounded-full bg-gray-200 h-32 w-32 mb-4"></div>
              <div className="h-6 bg-gray-200 rounded w-1/2 mb-4"></div>
              <div className="h-4 bg-gray-200 rounded w-1/4 mb-8"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
              <div className="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
              <div className="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
            </div>
          </div>
        </div>
        <Footer />
      </div>
    )
  }

  if (!profile) {
    return (
      <div className="min-h-screen bg-green-50">
        <Navbar />
        <div className="container mx-auto px-4 py-12">
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            {error || "Gagal memuat profil pengguna. Silakan coba lagi nanti."}
          </div>
          <div className="mt-4 text-center">
            <button
              onClick={() => window.location.reload()}
              className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg"
            >
              Muat Ulang
            </button>
          </div>
        </div>
        <Footer />
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-green-50">
      <Navbar />

      <div className="container mx-auto px-4 py-12">
        <div className="max-w-4xl mx-auto">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{error}</div>
          )}

          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            {/* Profile Header */}
            <div className="bg-green-700 text-white p-6">
              <div className="flex flex-col md:flex-row items-center gap-6">
                <div className="relative">
                  <div className="w-32 h-32 rounded-full overflow-hidden bg-green-600 flex items-center justify-center">
                    {profile.avatar_url ? (
                      <Image
                        src={profile.avatar_url || "/placeholder.svg"}
                        alt={profile.full_name || "User avatar"}
                        width={128}
                        height={128}
                        className="object-cover w-full h-full"
                        onError={(e) => {
                          // Fallback if image fails to load
                          e.target.src = "/placeholder.svg?height=128&width=128&text=User"
                        }}
                      />
                    ) : (
                      <User size={64} />
                    )}
                  </div>

                  {isEditing && (
                    <label
                      htmlFor="avatar-upload"
                      className={`absolute bottom-0 right-0 bg-green-500 hover:bg-green-600 p-2 rounded-full cursor-pointer ${isUploading ? "opacity-50 cursor-not-allowed" : ""}`}
                      title="Unggah avatar baru"
                    >
                      {isUploading ? (
                        <div className="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white"></div>
                      ) : (
                        <Upload size={16} />
                      )}
                      <input
                        type="file"
                        id="avatar-upload"
                        className="hidden"
                        accept="image/*"
                        onChange={handleAvatarUpload}
                        disabled={isUploading}
                      />
                    </label>
                  )}
                </div>

                <div>
                  <h1 className="text-2xl font-bold">{profile.full_name || "Pengguna"}</h1>
                  <p className="text-green-200">{profile.username || "username"}</p>
                  <div className="mt-2 inline-block bg-green-600 px-3 py-1 rounded-full text-sm">
                    {profile.role === "expert" ? "Pakar Tanaman" : profile.role === "admin" ? "Admin" : "Pengguna"}
                  </div>
                </div>

                <div className="md:ml-auto flex flex-col sm:flex-row gap-3">
                  <button
                    onClick={() => setIsEditing(!isEditing)}
                    className="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg flex items-center gap-2"
                  >
                    <Edit size={16} />
                    {isEditing ? "Batal" : "Edit Profil"}
                  </button>

                  {profile.role !== "expert" && profile.role !== "admin" && !expertRequest && (
                    <Link
                      href="/request-expert"
                      className="bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded-lg flex items-center gap-2"
                    >
                      <Award size={16} />
                      Ajukan Sebagai Pakar
                    </Link>
                  )}

                  {expertRequest && expertRequest.status === "pending" && (
                    <div className="bg-yellow-100 text-yellow-800 py-2 px-4 rounded-lg flex items-center gap-2">
                      <Award size={16} />
                      Permintaan Pakar Sedang Diproses
                    </div>
                  )}

                  {expertRequest && expertRequest.status === "rejected" && (
                    <div className="bg-red-100 text-red-800 py-2 px-4 rounded-lg flex items-center gap-2">
                      <Award size={16} />
                      Permintaan Pakar Ditolak
                    </div>
                  )}
                </div>
              </div>
            </div>

            {/* Profile Content */}
            <div className="p-6">
              {isEditing ? (
                <form onSubmit={handleSubmit}>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                      <label htmlFor="username" className="block text-sm font-medium text-gray-700 mb-1">
                        Username
                      </label>
                      <input
                        type="text"
                        id="username"
                        name="username"
                        value={formData.username}
                        onChange={handleChange}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      />
                    </div>

                    <div>
                      <label htmlFor="full_name" className="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap
                      </label>
                      <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value={formData.full_name}
                        onChange={handleChange}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      />
                    </div>

                    <div className="md:col-span-2">
                      <label htmlFor="bio" className="block text-sm font-medium text-gray-700 mb-1">
                        Bio
                      </label>
                      <textarea
                        id="bio"
                        name="bio"
                        rows={4}
                        value={formData.bio}
                        onChange={handleChange}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Ceritakan tentang diri Anda..."
                      ></textarea>
                    </div>

                    <div>
                      <label htmlFor="website" className="block text-sm font-medium text-gray-700 mb-1">
                        Website
                      </label>
                      <input
                        type="url"
                        id="website"
                        name="website"
                        value={formData.website}
                        onChange={handleChange}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="https://example.com"
                      />
                    </div>
                  </div>

                  <div className="flex justify-end">
                    <button
                      type="submit"
                      disabled={isSaving}
                      className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 disabled:opacity-50"
                    >
                      {isSaving ? (
                        <>
                          <div className="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white"></div>
                          <span>Menyimpan...</span>
                        </>
                      ) : (
                        <>
                          <Save size={16} />
                          <span>Simpan Perubahan</span>
                        </>
                      )}
                    </button>
                  </div>
                </form>
              ) : (
                <div>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                      <h3 className="text-sm font-medium text-gray-500">Username</h3>
                      <p className="mt-1">{profile.username || "Belum diatur"}</p>
                    </div>

                    <div>
                      <h3 className="text-sm font-medium text-gray-500">Nama Lengkap</h3>
                      <p className="mt-1">{profile.full_name || "Belum diatur"}</p>
                    </div>

                    <div className="md:col-span-2">
                      <h3 className="text-sm font-medium text-gray-500">Bio</h3>
                      <p className="mt-1">{profile.bio || "Belum ada bio."}</p>
                    </div>

                    {profile.website && (
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Website</h3>
                        <a
                          href={profile.website}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="mt-1 text-green-600 hover:text-green-700"
                        >
                          {profile.website}
                        </a>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Activity Section */}
        </div>
      </div>

      <Footer />
    </div>
  )
}
