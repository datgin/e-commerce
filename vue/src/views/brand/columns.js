const columns = [
  {
    title: 'TÊN THƯƠNG HIỆU',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.length - b.name.length,
  },
  {
    title: 'SLUG',
    dataIndex: 'slug',
    key: 'slug',
    sorter: (a, b) => a.slug.length - b.slug.length,
  },
  {
    title: 'ĐỊA CHỈ TRANG WEB',
    dataIndex: 'website_url',
    key: 'website_url',
  },
  {
    title: 'TRẠNG THÁI',
    dataIndex: 'published',
    key: 'published',
    width: '10%',
  },
]

export { columns }
