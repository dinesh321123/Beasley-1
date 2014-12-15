Formbuilder.registerField 'email',

  order: 40

  view: """
    <input type='text' data-fieldname='<%= rf.get(Formbuilder.options.mappings.LABEL) %>' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />
  """

  edit: ""

  addButton: """
    <span class="symbol"><span class="fa fa-envelope-o"></span></span> Email
  """
