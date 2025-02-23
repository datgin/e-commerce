const columns = [
  {
    title: 'TÊN',
    dataIndex: 'value',
    key: 'value',
    sorter: (a, b) => a.value.length - b.value.length,
    width: '22%',
  },
  {
    title: 'ĐƯỜNG DẪN TĨNH',
    dataIndex: 'slug',
    key: 'slug',
    sorter: (a, b) => a.slug.length - b.slug.length,
  },
  {
    title: 'MÔ TẢ',
    dataIndex: 'description',
    key: 'description',
  },
]

export { columns }
