const defaultTitle = '諾摩浪浪'

export default function getPageTitle(pageTitle?: string): string {
    if (pageTitle) {
        return `${pageTitle} - ${defaultTitle}`
    }
    return defaultTitle
}
