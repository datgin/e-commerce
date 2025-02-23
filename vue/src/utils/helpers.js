const getFirstError = (errors) => {
  const firstField = Object.keys(errors)[0] // Lấy trường đầu tiên từ lỗi
  if (firstField && errors[firstField] && errors[firstField].length > 0) {
    return errors[firstField][0] // Lấy lỗi đầu tiên của trường đó
  }
  return null // Nếu không có lỗi, trả về null
}

const getBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => resolve(reader.result)
    reader.onerror = (error) => reject(error)
  })
}

const getFileNameFromUrl = (url) => {
  const parts = url.split('/')
  return parts[parts.length - 1]
}

const getImageToAnt = (images) => {
  if (!images) return []

  // Kiểm tra nếu images là một chuỗi
  if (typeof images === 'string') {
    const fileName = getFileNameFromUrl(images)
    return [
      {
        uid: fileName,
        name: fileName,
        status: 'done',
        url: images,
      },
    ]
  }

  // Kiểm tra nếu images là một mảng
  if (Array.isArray(images)) {
    return images.map((image) => ({
      uid: getFileNameFromUrl(image),
      name: getFileNameFromUrl(image),
      status: 'done',
      url: image,
    }))
  }

  // Trường hợp còn lại, trả về mảng rỗng
  return []
}

const isJSONString = (str) => {
  try {
    JSON.parse(str)
    return true
  } catch (error) {
    return false
  }
}

const resizeImage = (image, width, height) => {
  if (!image) {
    return image
  }
  const params = []

  if (width) {
    params.push(`w=${width}`)
  }
  if (height) {
    params.push(`h=${height}`)
  }

  if (params.length > 0) {
    const separator = image.includes('?') ? '&' : '?'
    image += separator + params.join('&')
  }

  return image
}

function removeLastTwoWords(str) {
  return str.split(' ').slice(0, -2).join(' ')
}

export {
  getFirstError,
  getBase64,
  getFileNameFromUrl,
  getImageToAnt,
  isJSONString,
  resizeImage,
  removeLastTwoWords,
}
