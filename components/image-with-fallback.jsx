"use client"

import { useState } from "react"
import Image from "next/image"

export default function ImageWithFallback({
  src,
  alt,
  fallbackSrc,
  width,
  height,
  fill = false,
  style,
  className,
  ...props
}) {
  const [imgSrc, setImgSrc] = useState(src)
  const [error, setError] = useState(false)

  const handleError = () => {
    if (!error) {
      setImgSrc(
        fallbackSrc ||
          `/placeholder.svg?height=${height || 300}&width=${width || 300}&text=${encodeURIComponent(alt || "Image")}`,
      )
      setError(true)
    }
  }

  // Force the image to load from the actual URL by adding a cache-busting parameter
  const imageUrl = imgSrc && !imgSrc.includes("placeholder.svg") ? `${imgSrc}?t=${new Date().getTime()}` : imgSrc

  return (
    <Image
      src={imageUrl || "/placeholder.svg"}
      alt={alt || "Image"}
      width={!fill ? width : undefined}
      height={!fill ? height : undefined}
      fill={fill}
      style={style}
      className={className}
      onError={handleError}
      unoptimized={true}
      {...props}
    />
  )
}
