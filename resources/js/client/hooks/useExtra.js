// hooks/useExtra.js
import { useMemo } from 'react'

export function useExtra(item) {
  const extra = useMemo(() => {
    const merged = [
      ...(item?.config?.extra || []),
      ...(item?.config?.['sub-item']?.extra || [])
    ]

    return Object.fromEntries(
      merged.map(str => {
        const [key, val] = str.split(':')
        return [key, val ?? true]
      })
    )
  }, [item])

  const getExtra = (key) => extra[key] ?? false

  return { getExtra, extra }
}
