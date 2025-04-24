import "./globals.css"
import { UserProvider } from "@/components/user-provider"

export const metadata = {
  title: "HarvestWorld - Pendidikan Tanaman Pertanian",
  description: "Pelajari tentang tanaman pertanian, teknik budidaya, dan praktik pertanian berkelanjutan",
    generator: 'v0.dev'
}

export default function RootLayout({ children }) {
  return (
    <html lang="id">
      <body>
        <UserProvider>{children}</UserProvider>
      </body>
    </html>
  )
}
