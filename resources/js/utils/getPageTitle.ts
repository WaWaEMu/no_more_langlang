import { trans } from 'laravel-vue-i18n'

export default function getPageTitle(pageTitle?: string): string {
    const defaultTitle = trans('App Name') === 'App Name' ? '諾摩浪浪' : trans('App Name')
    if (pageTitle) {
        const translatedPageTitle = trans(pageTitle)
        return `${translatedPageTitle === pageTitle ? pageTitle : translatedPageTitle} - ${defaultTitle}`
    }
    return defaultTitle
}
