const columns = [
  {
    title: 'TÊN THUỘC TÍNH',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.length - b.name.length,
    width: '22%',
  },
  {
    title: 'ĐƯỜNG DẪN TĨNH',
    dataIndex: 'slug',
    key: 'slug',
    sorter: (a, b) => a.slug.length - b.slug.length,
  },
  {
    title: 'ITEMS',
    dataIndex: 'items',
    key: 'items',
  },
  {
    title: 'TRẠNG THÁI',
    dataIndex: 'published',
    key: 'published',
    width: '15%',
  },
]

export { columns }
