<div>
    <form wire:submit="submit" class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <h2>General</h2>
            <x-forms.button type="submit">
                Save
            </x-forms.button>
        </div>
        <div class="flex gap-2">
            <x-forms.input label="Name" id="name" />
            <x-forms.input label="Description" id="description" />
            <x-forms.input label="Image" id="image" required
                helper="For all available images, check here:<br><br><a target='_blank' href=https://hub.docker.com/r/eqalpha/keydb'>https://hub.docker.com/r/eqalpha/keydb</a>" />
        </div>

        @if ($database->started_at)
            <div class="flex gap-2">
                <x-forms.input label="Initial Password" id="keydbPassword" type="password" required readonly
                    helper="You can only change this in the database." />
            </div>
        @else
            <div class=" dark:text-warning">Please verify these values. You can only modify them before the initial
                start. After that, you need to modify it in the database.
            </div>
            <div class="flex gap-2">
                <x-forms.input label="Password" id="keydbPassword" type="password" required />
            </div>
        @endif
        <x-forms.input
            helper="You can add custom docker run options that will be used when your container is started.<br>Note: Not all options are supported, as they could mess up Coolify's automation and could cause bad experience for users.<br><br>Check the <a class='underline dark:text-white' href='https://coolify.io/docs/knowledge-base/docker/custom-commands'>docs.</a>"
            placeholder="--cap-add SYS_ADMIN --device=/dev/fuse --security-opt apparmor:unconfined --ulimit nofile=1024:1024 --tmpfs /run:rw,noexec,nosuid,size=65536k"
            id="customDockerRunOptions" label="Custom Docker Options" />
        <div class="flex flex-col gap-2">
            <h3 class="py-2">Network</h3>
            <div class="flex items-end gap-2">
                <x-forms.input placeholder="3000:5432" id="portsMappings" label="Ports Mappings"
                    helper="A comma separated list of ports you would like to map to the host system.<br><span class='inline-block font-bold dark:text-warning'>Example</span>3000:5432,3002:5433" />
            </div>
            <x-forms.input label="KeyDB URL (internal)"
                helper="If you change the user/password/port, this could be different. This is with the default values."
                type="password" readonly wire:model="dbUrl" />
            @if ($dbUrlPublic)
                <x-forms.input label="KeyDB URL (public)"
                    helper="If you change the user/password/port, this could be different. This is with the default values."
                    type="password" readonly wire:model="dbUrlPublic" />
            @else
                <x-forms.input label="KeyDB URL (public)"
                    helper="If you change the user/password/port, this could be different. This is with the default values."
                    readonly value="Starting the database will generate this." />
            @endif
        </div>
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center justify-between w-full">
                    <h3>SSL Configuration</h3>
                    @if ($database->enable_ssl && $certificateValidUntil)
                        <x-modal-confirmation title="Regenerate SSL Certificates"
                            buttonTitle="Regenerate SSL Certificates" :actions="[
                                'The SSL certificate of this database will be regenerated.',
                                'You must restart the database after regenerating the certificate to start using the new certificate.',
                            ]"
                            submitAction="regenerateSslCertificate" :confirmWithText="false" :confirmWithPassword="false" />
                    @endif
                </div>
            </div>
            @if ($database->enable_ssl && $certificateValidUntil)
                <span class="text-sm">Valid until:
                    @if (now()->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} - Expired</span>
                    @elseif(now()->addDays(30)->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} - Expiring
                            soon</span>
                    @else
                        <span>{{ $certificateValidUntil->format('d.m.Y H:i:s') }}</span>
                    @endif
                </span>
            @endif
            <div class="flex flex-col gap-2">
                <div class="w-64">
                    @if (str($database->status)->contains('exited'))
                        <x-forms.checkbox id="enable_ssl" label="Enable SSL" wire:model.live="enable_ssl"
                            instantSave="instantSaveSSL" />
                    @else
                        <x-forms.checkbox id="enable_ssl" label="Enable SSL" wire:model.live="enable_ssl"
                            instantSave="instantSaveSSL" disabled
                            helper="Database should be stopped to change this settings." />
                    @endif
                </div>
            </div>
        </div>
        <div>
            <div class="flex flex-col py-2 w-64">
                <div class="flex items-center gap-2 pb-2">
                    <div class="flex items-center">
                        <h3>Proxy</h3>
                        <x-loading wire:loading wire:target="instantSave" />
                    </div>
                    @if ($isPublic)
                        <x-slide-over fullScreen>
                            <x-slot:title>Proxy Logs</x-slot:title>
                            <x-slot:content>
                                <livewire:project.shared.get-logs :server="$server" :resource="$database"
                                    container="{{ data_get($database, 'uuid') }}-proxy" lazy />
                            </x-slot:content>
                            <x-forms.button disabled="{{ !$isPublic }}"
                                @click="slideOverOpen=true">Logs</x-forms.button>
                        </x-slide-over>
                    @endif
                </div>
                <x-forms.checkbox instantSave id="isPublic" label="Make it publicly available" />
            </div>
            <x-forms.input placeholder="5432" disabled="{{ $isPublic }}" id="publicPort" label="Public Port" />
        </div>
        <x-forms.textarea
            helper="<a target='_blank' class='underline dark:text-white' href='https://raw.githubusercontent.com/Snapchat/KeyDB/unstable/keydb.conf'>KeyDB Default Configuration</a>"
            label="Custom KeyDB Configuration" rows="10" id="keydbConf" />
    </form>
    <h3 class="pt-4">Advanced</h3>
    <div class="w-64">
        <x-forms.checkbox helper="Drain logs to your configured log drain endpoint in your Server settings."
            instantSave="instantSaveAdvanced" id="isLogDrainEnabled" label="Drain Logs" />
    </div>
</div>
