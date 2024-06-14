<template>
    <div id="sidebar_cover">
        <NcAppSidebar class="sidebar">
            <NcAppSidebarTab name="Thông Tin Cán Bộ" id="details" order="1">
                <Details :user-id="this.user_id"/>
            </NcAppSidebarTab>
            <NcAppSidebarTab name="Quá Trình Công Tác" id="business" order="2">
                <Business :user-id="this.user_id"/>
            </NcAppSidebarTab>
            <NcAppSidebarTab name="Quá Trình Đào Tạo" id="education" order="3">
                <Education :user-id="this.user_id"/>
            </NcAppSidebarTab>
            <NcAppSidebarTab name="Quá Trình Khen Thưởng" id="bonus" order="4">
                <Bonus :user-id="user_id"/>
            </NcAppSidebarTab>
            <NcAppSidebarTab name="Quá Trình Kỷ Luật" id="discipline" order="5">
                <Bonus :bonus-type="false" :user-id="user_id"/>
            </NcAppSidebarTab>
            <NcAppSidebarTab name="Thân Nhân" id="relation" order="6">
                <Relation :user-id="this.user_id"/>
            </NcAppSidebarTab>
        </NcAppSidebar>
    </div>
</template>
<script>
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { NcAppSidebar, NcAppSidebarTab, NcButton } from "@nextcloud/vue";
import Details from "./tab/Details.vue";
import Education from "./tab/Education.vue";
import Relation from "./tab/Relation.vue";
import Business from "./tab/Business.vue";
import Bonus from "./tab/Bonus.vue";

export default {
    name: 'Menu',
    props: ['user_id'],
    components: {
        NcAppSidebar,
        NcAppSidebarTab,
        NcButton,
        Details,
        Education,
        Relation,
        Business,
        Bonus
    },
}
</script>

<style scoped>
::v-deep .sidebar {
    max-width: none !important;
    width: 100% !important;
    height: 100% !important;
}

::v-deep .app-sidebar__close {
    display: none !important;
}

::v-deep .app-sidebar-header {
    height: 10px !important
}

#sidebar_cover {
    width: 100%;
}
</style>