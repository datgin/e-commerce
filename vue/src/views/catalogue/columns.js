const columns = [
  {
    title: 'NAME',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name), // Sắp xếp theo chữ cái
  },
  {
    title: 'SLUG',
    dataIndex: 'slug',
    key: 'slug',
    sorter: (a, b) => a.slug.localeCompare(b.slug), // Sắp xếp theo chữ cái
  },
  {
    title: 'PARENT CATALOG',
    dataIndex: 'parent_id',
    key: 'parent_id',
    sorter: (a, b) => (a.parent_name || '').localeCompare(b.parent_name || ''), // Sắp xếp parent (nếu có)
  },
  {
    title: 'PUBLISHED',
    dataIndex: 'published',
    key: 'published',
    width: '7%',
    sorter: (a, b) => Number(a.published) - Number(b.published), // Sắp xếp true/false
  },
];

export { columns };
