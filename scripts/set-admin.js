// This script sets the first user as an admin
// Run this script after creating your first user

const { createClient } = require("@supabase/supabase-js")

// Replace with your Supabase URL and SERVICE_ROLE key (not anon key)
const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL
const supabaseServiceKey = process.env.SUPABASE_SERVICE_ROLE_KEY

// Create a Supabase client with the service role key
const supabase = createClient(supabaseUrl, supabaseServiceKey)

async function setFirstUserAsAdmin() {
  try {
    // Get the first user from the profiles table
    const { data: profiles, error } = await supabase
      .from("profiles")
      .select("id")
      .limit(1)
      .order("created_at", { ascending: true })

    if (error) {
      throw error
    }

    if (profiles.length === 0) {
      console.log("No users found in the database")
      return
    }

    const firstUserId = profiles[0].id

    // Update the user's role to admin
    const { data, error: updateError } = await supabase
      .from("profiles")
      .update({ role: "admin" })
      .eq("id", firstUserId)
      .select()

    if (updateError) {
      throw updateError
    }

    console.log(`User with ID ${firstUserId} has been set as admin`)
    console.log("Updated profile:", data)
  } catch (error) {
    console.error("Error setting admin:", error)
  }
}

setFirstUserAsAdmin()
