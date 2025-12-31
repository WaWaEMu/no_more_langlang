import zh_TW from '../../../lang/zh_TW.json'

const messages: Record<string, any> = {
    zh_TW
}

// Simple translation helper
export function t(key: string): string {
    const keys = key.split('.')
    let result: any = messages.zh_TW // Default to zh_TW for now

    for (const k of keys) {
        if (result && result[k]) {
            result = result[k]
        } else {
            return key // Return key if not found
        }
    }

    return result
}
