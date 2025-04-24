"use client"

import { createContext, useContext, useEffect, useState } from "react"
import { supabase } from "@/lib/supabase"

const UserContext = createContext(null)

export function UserProvider({ children }) {
  const [user, setUser] = useState(null)
  const [profile, setProfile] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Mendapatkan sesi saat ini
    const getSession = async () => {
      try {
        const {
          data: { session },
        } = await supabase.auth.getSession()

        console.log("Session check:", session ? "Found session" : "No session")

        if (session?.user) {
          setUser(session.user)

          // Fetch user profile to get role information
          const { data } = await supabase.from("profiles").select("*").eq("id", session.user.id).single()

          if (data) {
            console.log("Profile loaded:", data.username || data.email)
            setProfile(data)
          } else {
            console.log("No profile found for user")
          }
        } else {
          setUser(null)
          setProfile(null)
        }
      } catch (error) {
        console.error("Error checking session:", error)
      } finally {
        setLoading(false)
      }
    }

    getSession()

    // Mendengarkan perubahan autentikasi
    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange(async (_event, session) => {
      console.log("Auth state changed:", _event, session ? "Has session" : "No session")

      if (session?.user) {
        setUser(session.user)

        // Fetch user profile to get role information
        const { data } = await supabase.from("profiles").select("*").eq("id", session.user.id).single()

        if (data) {
          console.log("Profile updated:", data.username || data.email)
          setProfile(data)
        }
      } else {
        setUser(null)
        setProfile(null)
      }

      setLoading(false)
    })

    return () => {
      subscription.unsubscribe()
    }
  }, [])

  const value = {
    user,
    profile,
    loading,
    isExpert: profile?.role === "expert",
    isAdmin: profile?.role === "admin",
    signOut: async () => {
      const { error } = await supabase.auth.signOut()
      if (error) console.error("Error signing out:", error)
      return { error }
    },
  }

  return <UserContext.Provider value={value}>{children}</UserContext.Provider>
}

export const useUser = () => {
  const context = useContext(UserContext)
  if (context === null) {
    throw new Error("useUser must be used within a UserProvider")
  }
  return context
}
