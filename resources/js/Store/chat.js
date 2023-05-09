import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useChatStore = defineStore(
    'domains',
    () => {
        const domains = ref('[]')

        function hasDomainId(domains, domainId) {
            let matches = domains.filter(d => {
                const domain = JSON.parse(d)

                return domain.domainId == domainId
            })

            return matches.length === 0 ? false : true
        }

        function dropDomainId(domains, domainId) {
            let newDomains = domains.filter(d => {
                const domain = JSON.parse(d)

                return domain.domainId != domainId
            })

            return newDomains
        }

        function addChatId(domainId, chatId) {
            let domains = JSON.parse(this.domains)
            
            if (this.hasDomainId(domains, domainId)) {
                domains = this.dropDomainId(domains, domainId)
            }

            let chat = JSON.stringify({
                'domainId': domainId,
                'chatId': chatId
            })

            domains.push(chat)

            this.domains = JSON.stringify(domains)
        }

        function getChatId(domainId) {
            const domains = JSON.parse(this.domains ?? '[]')

            let chatId  = ''

            domains.forEach(d => {
                const domain = JSON.parse(d)

                domain.domainId == domainId
                    ? chatId = domain.chatId
                    : ''

                return
            })

            return chatId
        }

        return {
            domains,
            hasDomainId,
            dropDomainId,
            addChatId,
            getChatId,
        }
    },
    {
        persist: true,
    }
)
