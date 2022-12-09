export const ADV_FILTERS_LIMIT = 7 //for advanced filters client-side paging

export const filtersFromServerFormat = function (advancedFilters) {
  const allFilters = {}
  const pages = {}
  for (let groupName in advancedFilters) {
    let filters = advancedFilters[groupName].data
    allFilters[groupName] = filters
    pages[groupName] = {count: ADV_FILTERS_LIMIT, totalCount: filters.length}
  }
  return {pages, allFilters}
}
