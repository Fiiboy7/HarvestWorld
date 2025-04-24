import Link from "next/link"
import { CheckCircle } from "lucide-react"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"

export default function SignupSuccessPage() {
  return (
    <div className="min-h-screen bg-green-50 flex flex-col">
      <Navbar />

      <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-md w-full text-center">
          <div className="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
            <CheckCircle className="h-12 w-12 text-green-600" />
          </div>

          <h2 className="mt-6 text-3xl font-extrabold text-gray-900">Pendaftaran Berhasil!</h2>

          <p className="mt-4 text-lg text-gray-600">
            Terima kasih telah mendaftar di HarvestWorld. Kami telah mengirimkan email konfirmasi ke alamat email Anda.
          </p>

          <p className="mt-2 text-gray-600">
            Silakan periksa kotak masuk Anda dan klik tautan konfirmasi untuk mengaktifkan akun Anda.
          </p>

          <div className="mt-8">
            <Link
              href="/login"
              className="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
            >
              Lanjut ke Halaman Login
            </Link>
          </div>

          <p className="mt-4 text-sm text-gray-500">
            Tidak menerima email?{" "}
            <button className="font-medium text-green-600 hover:text-green-500">Kirim ulang email konfirmasi</button>
          </p>
        </div>
      </div>

      <Footer />
    </div>
  )
}
