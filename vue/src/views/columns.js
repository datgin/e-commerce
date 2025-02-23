const columns = [
  {
    data: 'name',
    title: 'TÊN',
    render: function (data, type, row) {
      return `<a href="#" class="btn-edit text-dark fw-bold text-decoration-none" data-url="${`/products/${row.id}/edit`}">${data}</a>`
    },
  },
  { data: 'email', title: 'EMAIL' },
  { data: 'role_id', name: 'role_id', title: 'CHỨC VỤ' },
  { data: 'created_at', title: 'NGÀY TẠO', render: (data) => new Date(data).toLocaleString() },
  { data: 'published', title: 'TRẠNG THÁI', width: '10%', orderable: false, searchable: false },
]

export default columns
