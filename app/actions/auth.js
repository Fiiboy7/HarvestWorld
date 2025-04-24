"use server"

import { supabase } from "@/lib/supabase"
import { cookies } from "next/headers"

// Create a Supabase client with cookies
const createServerClient = () => {
  const cookieStore = cookies()

  return supabase
}

// Mendaftar dengan email dan password
export async function signUp(email, password, firstName, lastName) {
  const supabaseServer = createServerClient()

  const { data, error } = await supabaseServer.auth.signUp({
    email,
    password,
    options: {
      data: {
        first_name: firstName,
        last_name: lastName,
        full_name: `${firstName} ${lastName}`,
      },
    },
  })

  if (error) {
    console.error("Error signing up:", error)
    return { error: error.message }
  }

  return { user: data.user }
}

// Login dengan email dan password
export async function signIn(email, password) {
  const supabaseServer = createServerClient()

  const { data, error } = await supabaseServer.auth.signInWithPassword({
    email,
    password,
  })

  if (error) {
    console.error("Error signing in:", error)
    return { error: error.message }
  }

  return { user: data.user, session: data.session }
}

// Logout
export async function signOut() {
  const supabaseServer = createServerClient()

  const { error } = await supabaseServer.auth.signOut()

  if (error) {
    console.error("Error signing out:", error)
    return { error: error.message }
  }

  return { success: true }
}

// Mendapatkan user saat ini
export async function getCurrentUser() {
  const supabaseServer = createServerClient()

  const {
    data: { user },
  } = await supabaseServer.auth.getUser()

  return user
}
